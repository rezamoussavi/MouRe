{\rtf1\ansi\ansicpg1252\cocoartf1038\cocoasubrtf320
{\fonttbl\f0\fswiss\fcharset0 Helvetica;}
{\colortbl;\red255\green255\blue255;}
\margl1440\margr1440\vieww9000\viewh8400\viewkind0
\pard\tx566\tx1133\tx1700\tx2267\tx2834\tx3401\tx3968\tx4535\tx5102\tx5669\tx6236\tx6803\ql\qnatural\pardirnatural

\f0\fs24 \cf0 CREATE TABLE `user_info` (\
  `userUID` int(11) NOT NULL AUTO_INCREMENT,\
  `email` varchar(50) NOT NULL,\
  `password` varchar(50) NOT NULL,\
  `verificationCode` varchar(50) NOT NULL,\
  `biznessUID` int(11) NOT NULL,\
  PRIMARY KEY (`userUID`),\
  UNIQUE KEY `email` (`email`)\
) }