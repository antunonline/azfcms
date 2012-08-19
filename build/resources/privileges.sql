INSERT IGNORE INTO Acl (resource,description) VALUES 
('cms.userStereotype.root','Root stereotype resource is administrative resource which gives all 
            rights to the granting user.')
,
('resource.admin.rw','Read and write admin access')
,
('resource.user.login','User login privilege. Used by guest users to access their registered accounts.')
;
