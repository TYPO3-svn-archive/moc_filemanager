
package updater;

use strict;
use DBI;
use POSIX qw(strftime);

sub new {
    my($class) = shift;
    my $rootdir = shift;
    
   
    my $self = {};
    $self->{currentmp} = {};
    $self->{updatedentries}=0;
    $self->{insertedentries}=0;
    $self->{checkedmps}=0;
    $self->{errors} = 0;
    
    bless $self,$class;
    $self->{rootdir} = $self->cleandir($rootdir);
    return $self;
}
sub DESTROY {
    my $self = shift;
    if($self->{DB}) {
	$self->{DB}->disconnect();
    }

}
sub connect {
    my $self = shift;
    my $dbname = shift;
    my $dbuser = shift;
    my $dbpassword= shift;
    $self->log("Connecting to database $dbname with user $dbuser");
    if(!($self->{DB} = DBI->connect("DBI:mysql:$dbname", $dbuser, $dbpassword))) {
	print "Error connecting to DB\n";
	exit;
    }

#    $self->{DB}->disconnect();
}
sub start() {
    my $self = shift;
    if(!$self->{DB}) {
		  $self->log("Not connected to DB! Exiting.","ERROR");
		  return;
    }
    my $sth = $self->{DB}->prepare("SELECT uid,pid,path,name from tx_mocfilemanager_mounts");
    my $row;
    if(!($sth->execute)) {
		  $sth->finish;
		  $self->{DB}->disconnect();
		  print "Error in SQL statement\n";
		  exit;
    }
    while($row = $sth->fetchrow_hashref()) {
		  if( -d $self->{rootdir}."/".$row->{path})  {
				$self->{checkedmps}++;
				$self->{currentmp}->{uid} = $row->{uid};
				$self->{currentmp}->{path} = $self->cleandir($row->{path});
				$self->{currentmp}->{pid} = $row->{pid};
				$self->checkdir("");
		  }
		  else {
				$self->{errors}++;
				$self->log($self->{rootdir}."/".$row->{path}." is not a valid path!","ERROR");
		  }
    }
    $sth->finish();
}
sub checkdir {
    my $self = shift;
    my $dir = $self->cleandir(shift);
    my $level = shift || 0;

    my $fulldir = $self->cleandir($self->{rootdir}."/".$self->{currentmp}->{path}."/".$dir);
    $self->log("Starting index of ".$fulldir,"DEBUG");
    if(!opendir(DIR, $fulldir)) {
	$self->log("can't opendir ".$self->{rootdir}."/".$self->{currentmp}->{path}."/".$dir.": $!","ERROR");
	return;
    }

    my @dots = grep { !/^\./} readdir(DIR);   
    closedir DIR; 
    my $test;

    foreach $test(@dots) {
	my $testentry = $dir eq "" ? $test : "$dir/$test";
	if(-d "$fulldir/$test") {
	    #print "Entering $test \n";
	    $self->checkentry("$testentry","dir");
	    $self->checkdir("$testentry",$level+1);
	}
	elsif(-f "$fulldir/$test") {
	    $self->checkentry("$testentry","file");
	}
	
    }

}
sub checkentry {
    my $self = shift;
    my $entry = shift;
    my $entrytype=shift;

   
    my ($mount_uid,$mount_path,$mount_pid) = ($self->{currentmp}->{uid},$self->{currentmp}->{path},$self->{currentmp}->{pid});
    my @insert_fields;
    my @insert_values;

    my $needs_update = 0;
    my $sth = $self->{DB}->prepare("SELECT * from tx_mocfilemanager_files WHERE file=? AND mount=?");
    $sth->bind_param(1,"$entry");
    $sth->bind_param(2,"$mount_uid");
    $sth->execute();
    my ($dev,$ino,$mode,$nlink,$uid,$gid,$rdev,$size,$atime,$mtime,$ctime,$blksize,$blocks) = stat($self->{rootdir}."/".$self->{currentmp}->{path}."/".$entry);
    if($sth->rows > 0) {
	my $SQL = "UPDATE tx_mocfilemanager_files SET ";
	#$self->log("$entry existed");
	my $row = $sth->fetchrow_hashref();
	#Determine what fields neds update.

	if($row->{uploaded} == 0) {
	    $needs_update = 1;
	    $SQL .= "uploaded='".$mtime."' ";
	}
	if($row->{size} == 0 && $entrytype eq "file") {
	    $needs_update = 1;
	    $SQL .= "size='".$size."' ";
	}
	$SQL .= "WHERE uid=".$row->{uid};
	if($needs_update == 1) {	
	    $self->{DB}->do($SQL);
	    $self->log("Updated entry uid: ".$row->{uid}." $entry","DEBUG");
	    $self->{updatedentries}++;
	}
    }
    else {
	$sth = $self->{DB}->prepare("INSERT INTO tx_mocfilemanager_files (file,uploaded,type,mount,size,pid,tstamp,crdate) VALUES (?,?,?,?,?,?,?,?)");
	my $date = time();
	$sth->bind_param(1,$entry);
	$sth->bind_param(2,$mtime);
	$sth->bind_param(3,($entrytype eq "dir" ? 1: 0));
	$sth->bind_param(4,$self->{currentmp}->{uid});
	$sth->bind_param(5,$size);
	$sth->bind_param(6,$self->{currentmp}->{pid});	
	$sth->bind_param(7,$date);
	$sth->bind_param(8,$date);
#	print "INSERT INTO tx_mocfilemanager_files (file,uploaded,type,mount,size,pid) VALUES ('".$entry."',".$mtime.",".$entrytype.",".$self->{currentmp}->{uid}.",?,?)\n";
	
#	print "I want to insert file $entry into DB\n";
	$sth->execute();
	$self->{insertedentries}++;
	$self->log("Added $entry to mp with uid : ".$self->{currentmp}->{uid},"DEBUG");
    }


    $sth->finish();
}
sub cleandir {
    my $self = shift;
    my $entry = shift;
    $entry =~ s/[\/]$//;
    return $entry;
}

sub writeStats {
    my $self = shift;
    my $filename = shift;
    if($filename) {
		  open STATFILE, ">$filename";
		  print STATFILE "<h4>Filopdateringstjeneste</h4>\n";	
		  print STATFILE "<table>\n <tr>\n";
		  print STATFILE "  <td>K&oslash;rte sidste gange</td>\n";
		  print STATFILE "  <td>".strftime("%a %b %e %H:%M:%S %Y", localtime)."</td>\n </tr>\n";
		  print STATFILE " <tr>  <td>Opdaterede filer/mapper</td>\n  <td>".$self->{updatedentries}."</td>\n </tr>\n";
		  print STATFILE " <tr>\n  <td>Nye filer/mapper</td>\n  <td>".$self->{insertedentries}."</td>\n </tr>\n";
		  print STATFILE " <tr>\n  <td>Fejl undervejs: </td>\n  <td>".$self->{errors}."</td>\n </tr>\n";
		  print STATFILE " <tr>\n  <td>Tjekkede mountpoints:</td>\n  <td>".$self->{checkedmps}."</td>\n </tr>\n";
		  print STATFILE " </table>\n";
		  close STATFILE;
    }
	 
#    $self->log("Dameon updated: ".$self->{updatedentries}." files/dirs");
#    $self->log("Dameon inserted: ".$self->{insertedentries}." files/dirs");
#    $self->log("Daemon encountered: ".$self->{errors}." during the run.");
#    $self->log("Daemon checked: ".$self->{checkedmps}." mountpoints during the run.");
    
}
sub log {
    my $self = shift;
    my $msg = shift;
    my $status = shift || "INFO";
    if($status ne "DEBUG") {
	if($self->{logToFile} == 1) {
	    open LOGFILE,">>logfile.html";
	    print LOGFILE "$status: $msg\n";
	    close LOGFILE;
	}
	else {
	    print "$status: $msg\n";
	}
    }
    if($status eq "ERROR") {
	#push($self->{errorsText},$msg);
    }
}
return 1;    
