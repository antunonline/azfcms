CREATE  TABLE IF NOT EXISTS `azfcms`.`User_ACLGroup` (
  `id` INT UNSIGNED NOT NULL ,
  `userId` INT UNSIGNED NOT NULL ,
  `aclGroupId` INT UNSIGNED NOT NULL ,
  PRIMARY KEY (`id`) ,
  INDEX `userId` (`userId` ASC) ,
  INDEX `aclGroupId` (`aclGroupId` ASC) ,
  CONSTRAINT `userId`
    FOREIGN KEY (`userId` )
    REFERENCES `azfcms`.`User` (`id` )
    ON DELETE CASCADE
    ON UPDATE NO ACTION,
  CONSTRAINT `aclGroupId`
    FOREIGN KEY (`aclGroupId` )
    REFERENCES `azfcms`.`ACLGroup` (`id` )
    ON DELETE CASCADE
    ON UPDATE NO ACTION)
ENGINE = InnoDB