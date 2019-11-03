CREATE TABLE `model` (
  `id` bigint(20) NOT NULL,
  `name` varchar(32) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

ALTER TABLE `model`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `model`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;

CREATE TABLE `model_action` (
  `id` bigint(20) NOT NULL,
  `model_id` bigint(20) NOT NULL,
  `name` varchar(32) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

ALTER TABLE `model_action`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `model_action` (`model_id`,`name`),
  ADD KEY `model_id` (`model_id`);

ALTER TABLE `model_action`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;

ALTER TABLE `model_action`
  ADD FOREIGN KEY (`model_id`)
    REFERENCES `model` (`id`)
    ON UPDATE CASCADE
    ON DELETE CASCADE;
    
CREATE TABLE `user` (
  `id` bigint(20) NOT NULL,
  `name` varchar(32) NOT NULL,
  `password` varchar(60) NOT NULL,
  `email` varchar(80) NOT NULL,
  `created_utc` int(11) NOT NULL,
  `updated_utc` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

ALTER TABLE `user`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`);

ALTER TABLE `user`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;

CREATE TABLE `role` (
  `id` bigint(20) NOT NULL,
  `name` varchar(32) NOT NULL,
  `description` varchar(80) NOT NULL,
  `signup_default` boolean NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

ALTER TABLE `role`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`);

ALTER TABLE `role`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;

CREATE TABLE `user_role` (
  `id` bigint(20) NOT NULL,
  `user_id` bigint(20) NOT NULL,
  `role_id` bigint(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

ALTER TABLE `user_role`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `user_role` (`user_id`,`role_id`),
  ADD KEY `user` (`user_id`),
  ADD KEY `role` (`role_id`);

ALTER TABLE `user_role`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;
  
ALTER TABLE `user_role`
  ADD FOREIGN KEY (`user_id`)
    REFERENCES `user` (`id`)
    ON UPDATE CASCADE
    ON DELETE CASCADE;
    
ALTER TABLE `user_role`
  ADD FOREIGN KEY (`role_id`)
    REFERENCES `role` (`id`)
    ON UPDATE CASCADE
    ON DELETE CASCADE;

CREATE TABLE `role_permission` (
  `id` bigint(20) NOT NULL,
  `role_id` bigint(20) NOT NULL,
  `model_action_id` bigint(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

ALTER TABLE `role_permission`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `role_model_action` (`role_id`,`model_action_id`),
  ADD KEY `role` (`role_id`),
  ADD KEY `model_action` (`model_action_id`);

ALTER TABLE `role_permission`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;
    
ALTER TABLE `role_permission`
  ADD FOREIGN KEY (`role_id`)
    REFERENCES `role` (`id`)
    ON UPDATE CASCADE
    ON DELETE CASCADE;
  
ALTER TABLE `role_permission`
  ADD FOREIGN KEY (`model_action_id`)
    REFERENCES `model_action` (`id`)
    ON UPDATE CASCADE
    ON DELETE CASCADE;
    


