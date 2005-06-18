-- MySQL dump 8.21
--
-- Host: localhost    Database: fow
---------------------------------------------------------
-- Server version	3.23.49-log

--
-- Table structure for table 'tx_mocfilemanager_files'
--

CREATE TABLE tx_mocfilemanager_files (
  uid int(11) unsigned NOT NULL auto_increment,
  pid int(11) unsigned NOT NULL default '0',
  tstamp int(11) unsigned NOT NULL default '0',
  crdate int(11) unsigned NOT NULL default '0',
  cruser_id int(11) unsigned NOT NULL default '0',
  file tinytext NOT NULL,
  uploaded int(11) NOT NULL default '0',
  type int(11) unsigned NOT NULL default '0',
  mount int(11) unsigned NOT NULL default '0',
  size int(11) unsigned NOT NULL default '0',
  comment text NOT NULL,
  downloads int(11) unsigned NOT NULL default '0',
  PRIMARY KEY  (uid),
  KEY parent (pid)
) TYPE=MyISAM;

--
-- Dumping data for table 'tx_mocfilemanager_files'
--


INSERT INTO tx_mocfilemanager_files VALUES (1,0,0,0,0,'fowsetup.msi',1095591319,0,5,366592,'',7);
INSERT INTO tx_mocfilemanager_files VALUES (2,0,0,0,0,'InstMsiA.Exe',1096638008,0,5,1707856,'',4);
INSERT INTO tx_mocfilemanager_files VALUES (3,0,0,0,0,'Setup.Exe',1096638010,0,5,65536,'',6);
INSERT INTO tx_mocfilemanager_files VALUES (4,0,0,0,0,'InstMsiW.Exe',1096638010,0,5,1821008,'',6);
INSERT INTO tx_mocfilemanager_files VALUES (5,0,0,0,0,'Setup.Ini',1096638011,0,5,39,'',8);
INSERT INTO tx_mocfilemanager_files VALUES (6,0,0,0,0,'wowSetup.msi',1096638012,0,5,828416,'',1);
INSERT INTO tx_mocfilemanager_files VALUES (7,0,0,0,0,'Forbrug__Artikler_TVDS_V2.txt',1096829399,0,5,22538,'',2);
INSERT INTO tx_mocfilemanager_files VALUES (8,0,0,0,0,'logo3.gif',1096832922,0,5,1450,'',0);
INSERT INTO tx_mocfilemanager_files VALUES (9,0,0,0,0,'Dump001.gif',1096885955,0,5,65345,'',1);
INSERT INTO tx_mocfilemanager_files VALUES (10,0,0,0,0,'Rasmus2.jpg',1096908303,0,5,4007,'A new comment',2);
INSERT INTO tx_mocfilemanager_files VALUES (11,0,0,0,0,'Release/Commentdir',1097187552,0,5,0,'',0);
INSERT INTO tx_mocfilemanager_files VALUES (12,0,0,0,0,'Release/fowsetup.msi',1096638369,0,5,367616,'',0);
INSERT INTO tx_mocfilemanager_files VALUES (13,0,0,0,0,'Release/InstMsiA.Exe',1096638426,0,5,1707856,'',0);
INSERT INTO tx_mocfilemanager_files VALUES (14,0,0,0,0,'Release/InstMsiW.Exe',1096638487,0,5,1821008,'',0);
INSERT INTO tx_mocfilemanager_files VALUES (15,0,0,0,0,'Release/Setup.Exe',1096638489,0,5,65536,'',0);
INSERT INTO tx_mocfilemanager_files VALUES (47,0,0,0,0,'Release/Setup.Ini',1097665097,0,5,39,'',6);
INSERT INTO tx_mocfilemanager_files VALUES (17,0,0,0,0,'Release/folder_small.gif',1097186670,0,5,261,'',0);
INSERT INTO tx_mocfilemanager_files VALUES (18,0,0,0,0,'Release/folder_upTEST.gif',1097186733,0,5,1012,'',0);
INSERT INTO tx_mocfilemanager_files VALUES (19,0,0,0,0,'Release/folder.gif',1097187062,0,5,379,'',0);
INSERT INTO tx_mocfilemanager_files VALUES (20,0,0,0,0,'Release/folder_small_01TES1T.gif',1097187287,0,5,261,'',0);
INSERT INTO tx_mocfilemanager_files VALUES (21,0,0,0,0,'Release/folder_small_02TEST.gif',1097187294,0,5,261,'',0);
INSERT INTO tx_mocfilemanager_files VALUES (22,0,0,0,0,'Release/folder_small_03test.gif',1097187376,0,5,261,'',0);
INSERT INTO tx_mocfilemanager_files VALUES (23,0,0,0,0,'Release/folder_small_04test.gif',1097187453,0,5,261,'',0);
INSERT INTO tx_mocfilemanager_files VALUES (24,0,0,0,0,'Release/folder_up.gif',1097187470,0,5,1012,'',0);
INSERT INTO tx_mocfilemanager_files VALUES (25,0,0,0,0,'Release',1097189583,0,5,0,'',3);
INSERT INTO tx_mocfilemanager_files VALUES (26,0,0,0,0,'test',1096832159,0,1,0,'',0);
INSERT INTO tx_mocfilemanager_files VALUES (27,0,0,0,0,'test_2',1096832395,0,1,0,'',0);
INSERT INTO tx_mocfilemanager_files VALUES (28,0,0,0,0,'Forbrug__Artikler_TVDS_V2.txt',1096831543,0,1,22538,'',0);
INSERT INTO tx_mocfilemanager_files VALUES (29,0,0,0,0,'logo3.gif',1096832913,0,1,1450,'',1);
INSERT INTO tx_mocfilemanager_files VALUES (30,0,0,0,0,'dump02.gif',1096884307,0,1,17069,'',0);
INSERT INTO tx_mocfilemanager_files VALUES (31,0,0,0,0,'dump04.gif',1096884928,0,1,53489,'',0);
INSERT INTO tx_mocfilemanager_files VALUES (32,0,0,0,0,'dump03.gif',1096884989,0,1,60303,'',0);
INSERT INTO tx_mocfilemanager_files VALUES (33,0,0,0,0,'Fog of war client install package',1097857490,0,5,0,'',0);
INSERT INTO tx_mocfilemanager_files VALUES (34,0,0,0,0,'Fog of war client install package/readme.txt',1097856091,0,5,537,'',0);
INSERT INTO tx_mocfilemanager_files VALUES (35,0,0,0,0,'Fog of war client install package/Fog of War 1.0.010.exe',1097857624,0,5,3843928,'',0);
INSERT INTO tx_mocfilemanager_files VALUES (36,0,0,0,0,'Fog of war client install package/Fog of War 1.0.011.exe',1097947186,0,5,3849407,'',0);
INSERT INTO tx_mocfilemanager_files VALUES (37,0,0,0,0,'Fog of war client install package/Fog of War 1.0.012.exe',1097956713,0,5,3850699,'',0);
INSERT INTO tx_mocfilemanager_files VALUES (38,0,0,0,0,'Fog of war client install package/Fog of War 1.0.013.exe',1097968739,0,5,3851505,'',0);
INSERT INTO tx_mocfilemanager_files VALUES (39,0,0,0,0,'dotnetfx.exe',1098050019,0,5,24265736,'.NET 1.1 Installation package. Needed to run FOW.',0);
INSERT INTO tx_mocfilemanager_files VALUES (40,0,0,0,0,'Fog of war client install package/Fog of War 1.0.014.exe',1098134194,0,5,3852222,'',0);
INSERT INTO tx_mocfilemanager_files VALUES (41,0,0,0,0,'Fog of war client install package/Fog of War 1.0.015.exe',1098225147,0,5,3859689,'',1);
INSERT INTO tx_mocfilemanager_files VALUES (42,0,0,0,0,'Fog of war client install package/Fog of War 1.0.016.exe',1098531949,0,5,73728,'',1);
INSERT INTO tx_mocfilemanager_files VALUES (43,0,0,0,0,'Fog of war client install package/Fog of War 1.0.017.exe',1098639164,0,5,3867317,'',0);
INSERT INTO tx_mocfilemanager_files VALUES (44,0,0,0,0,'Fog of war client install package/Fog of War 1.0.018.exe',1098746231,0,5,3866768,'',3);
INSERT INTO tx_mocfilemanager_files VALUES (46,2,1099647932,1099647932,10,'bordfodbold.gif',1099647932,0,5,18253,'Min egen lille testfil',2);
INSERT INTO tx_mocfilemanager_files VALUES (48,0,0,0,0,'Fog of war client install package/Fog of War 0.9.019.exe',1099767460,0,5,3861755,'',1);
INSERT INTO tx_mocfilemanager_files VALUES (49,0,0,0,0,'Fog of war client install package/Fog of War 0.9.020.exe',1099857859,0,5,3861802,'',1);

