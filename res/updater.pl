#!/usr/bin/perl 


require updater;


my $db = shift;
my $user = shift;
my $pass = shift;
my $documentroot = shift;"/home/admnetfiles";

if($db eq "" || $user eq "" || $pass eq "" || $documentroot eq "") {
    print("Error must be called with db user pass docroot\n");
    exit;
}


my $upd = new updater($documentroot);
#$upd->connect("testbed","testbed","20com");
$upd->connect($db,$user,$pass);

#print "Test: ".$upd->cleandir("/home/Admnet/test/");

$upd->start();
$upd->writeStats('stats.html');
