INSERT INTO
    `ea_roles` (`id`, `name`, `slug`, `is_admin`, `appointments`, `customers`, `services`, `users`, `system_settings`, `user_settings`)
VALUES
    (1, 'Administrator', 'admin', 1, 15, 15, 15, 15, 15, 15),
    (2, 'Provider', 'provider', 0, 15, 0, 0, 0, 0, 15),
    (3, 'Customer', 'customer', 0, 0, 0, 0, 0, 0, 0),
    (4, 'Secretary', 'secretary', 0, 15, 15, 0, 0, 0, 15);

INSERT INTO `ea_settings` (`id_assessment_center`, `name`, `value`)
	select distinct `ac_id`, 'company_working_plan' as `name`, '{"sunday":{"start":"09:00","end":"18:00","breaks":[{"start":"11:20","end":"11:30"},{"start":"14:30","end":"15:00"}]},"monday":{"start":"09:00","end":"18:00","breaks":[{"start":"11:20","end":"11:30"},{"start":"14:30","end":"15:00"}]},"tuesday":{"start":"09:00","end":"18:00","breaks":[{"start":"11:20","end":"11:30"},{"start":"14:30","end":"15:00"}]},"wednesday":{"start":"09:00","end":"18:00","breaks":[{"start":"11:20","end":"11:30"},{"start":"14:30","end":"15:00"}]},"thursday":{"start":"09:00","end":"18:00","breaks":[{"start":"11:20","end":"11:30"},{"start":"14:30","end":"15:00"}]},"friday":{"start":"09:00","end":"18:00","breaks":[{"start":"11:20","end":"11:30"},{"start":"14:30","end":"15:00"}]},"saturday":{"start":"09:00","end":"18:00","breaks":[{"start":"11:20","end":"11:30"},{"start":"14:30","end":"15:00"}]}}' as `value` from `assessment_center_user` where `is_admin` = 1;
INSERT INTO `ea_settings` (`id_assessment_center`, `name`, `value`)
	select distinct `ac_id`, 'book_advance_timeout' as `name`, '30' as `value` from `assessment_center_user` where `is_admin` = 1;
INSERT INTO `ea_settings` (`id_assessment_center`, `name`, `value`)
	select distinct `ac_id`, 'company_name' as `name`, (select `name` from `assessment_center` where `id` = `acu`.`ac_id`) as `value` from `assessment_center_user` `acu` where `is_admin` = 1;
INSERT INTO `ea_settings` (`id_assessment_center`, `name`, `value`)
	select distinct `ac_id`, 'google_analytics_code' as `name`, '' as `value` from `assessment_center_user` where `is_admin` = 1;
INSERT INTO `ea_settings` (`id_assessment_center`, `name`, `value`)
	select distinct `ac_id`, 'company_email' as `name`, '' as `value` from `assessment_center_user` where `is_admin` = 1;
INSERT INTO `ea_settings` (`id_assessment_center`, `name`, `value`)
	select distinct `ac_id`, 'company_link' as `name`, (select `url` from `assessment_center` where `id` = `acu`.`ac_id`) as `value` from `assessment_center_user` `acu` where `is_admin` = 1;
INSERT INTO `ea_settings` (`id_assessment_center`, `name`, `value`)
	select distinct `ac_id`, 'customer_notifications' as `name`, '1' as `value` from `assessment_center_user` where `is_admin` = 1;
INSERT INTO `ea_settings` (`id_assessment_center`, `name`, `value`)
	select distinct `ac_id`, 'date_format' as `name`, 'DMY' as `value` from `assessment_center_user` where `is_admin` = 1;
INSERT INTO `ea_settings` (`id_assessment_center`, `name`, `value`)
	select distinct `ac_id`, 'time_format' as `name`, 'regular' as `value` from `assessment_center_user` where `is_admin` = 1;
INSERT INTO `ea_settings` (`id_assessment_center`, `name`, `value`)
	select distinct `ac_id`, 'require_captcha' as `name`, '0' as `value` from `assessment_center_user` where `is_admin` = 1;
INSERT INTO `ea_settings` (`id_assessment_center`, `name`, `value`)
	select distinct `ac_id`, 'display_cookie_notice' as `name`, '0' as `value` from `assessment_center_user` where `is_admin` = 1;
INSERT INTO `ea_settings` (`id_assessment_center`, `name`, `value`)
	select distinct `ac_id`, 'cookie_notice_content' as `name`, 'Cookie notice content.' as `value` from `assessment_center_user` where `is_admin` = 1;
INSERT INTO `ea_settings` (`id_assessment_center`, `name`, `value`)
	select distinct `ac_id`, 'display_terms_and_conditions' as `name`, '0' as `value` from `assessment_center_user` where `is_admin` = 1;
INSERT INTO `ea_settings` (`id_assessment_center`, `name`, `value`)
	select distinct `ac_id`, 'terms_and_conditions_content' as `name`, 'Terms and conditions content.' as `value` from `assessment_center_user` where `is_admin` = 1;
INSERT INTO `ea_settings` (`id_assessment_center`, `name`, `value`)
	select distinct `ac_id`, 'display_privacy_policy' as `name`, '0' as `value` from `assessment_center_user` where `is_admin` = 1;
INSERT INTO `ea_settings` (`id_assessment_center`, `name`, `value`)
	select distinct `ac_id`, 'privacy_policy_content' as `name`, 'Privacy policy content.' as `value` from `assessment_center_user` where `is_admin` = 1;

INSERT INTO `ea_migrations` (`version`) VALUES ('12');

INSERT INTO `ea_users` (`id`, `first_name`, `last_name`, `email`, `address`, `status`, `id_roles`, `id_assessment_center`)
    select
	u.id,
	u.name,
	u.lastname,
	u.email,
	u.address,
	acu.status,
        case
            when json_contains(u.roles, json_array('ac')) = 1 then 1
            when json_contains(u.roles, json_array('do')) = 1 then 2
            when json_contains(u.roles, json_array('student')) = 1 then 3
            else 4 end `role`,
        ac_id
        from assessment_center_user acu join user u on (u.id = acu.user_id);