 CREATE TABLE `user_info` (\
  `userUID` int(11) NOT NULL AUTO_INCREMENT,\
  `email` varchar(50) NOT NULL,\
  `password` varchar(50) NOT NULL,\
  `verificationCode` varchar(50) NOT NULL,\
  `biznessUID` int(11) NOT NULL,\
  PRIMARY KEY (`userUID`),\
  UNIQUE KEY `email` (`email`)\
) }