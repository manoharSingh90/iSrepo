/*
SQLyog Trial v13.1.2 (32 bit)
MySQL - 5.7.28-0ubuntu0.18.04.4 : Database - iSamplez-uat
*********************************************************************
*/

/*!40101 SET NAMES utf8 */;

/*!40101 SET SQL_MODE=''*/;

/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;
CREATE DATABASE /*!32312 IF NOT EXISTS*/`iSamplez-uat` /*!40100 DEFAULT CHARACTER SET utf8 */;

USE `iSamplez-uat`;

/*Table structure for table `admin` */

DROP TABLE IF EXISTS `admin`;

CREATE TABLE `admin` (
  `id` int(11) NOT NULL,
  `name` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `forgot_pass_identity` varchar(255) NOT NULL,
  `link_verified` tinyint(1) NOT NULL DEFAULT '0',
  `phone` varchar(16) NOT NULL,
  `user_type` tinyint(1) NOT NULL DEFAULT '2' COMMENT '1:admin,2:staff',
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '1:active,0:Inactive',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

/*Table structure for table `age_brackets` */

DROP TABLE IF EXISTS `age_brackets`;

CREATE TABLE `age_brackets` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `age_bracket_desc` varchar(64) NOT NULL,
  `is_active` tinyint(1) NOT NULL COMMENT '1 - Active\n0 - Inactive',
  `created_dttm` datetime NOT NULL,
  `modified_dttm` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8 COMMENT='This table will store the master data for age brackets drop-down. Values would be: Below 18, 18-25, 25-45, 45+';

/*Table structure for table `app_rates` */

DROP TABLE IF EXISTS `app_rates`;

CREATE TABLE `app_rates` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL,
  `rating` float DEFAULT NULL,
  `comment` text,
  `is_active` int(1) DEFAULT '1',
  `created_dttm` datetime DEFAULT NULL,
  `modified_dttm` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `audience_interest_options` */

DROP TABLE IF EXISTS `audience_interest_options`;

CREATE TABLE `audience_interest_options` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `targetaudience_id` int(11) NOT NULL,
  `interest_ques_id` int(11) NOT NULL,
  `interest_options_id` varchar(256) NOT NULL,
  `is_active` tinyint(1) NOT NULL,
  `created_dttm` datetime DEFAULT NULL,
  `modified_dttm` date DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `brand_assets` */

DROP TABLE IF EXISTS `brand_assets`;

CREATE TABLE `brand_assets` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `brand_id` int(11) DEFAULT NULL,
  `asset_name` varchar(64) NOT NULL,
  `asset_type` tinyint(2) unsigned NOT NULL COMMENT '1 - Image\n2 - Video',
  `asset_url` varchar(256) NOT NULL,
  `is_active` tinyint(1) NOT NULL COMMENT '1 - Active\n0 - Inactive',
  `created_dttm` datetime NOT NULL,
  `modified_dttm` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=130 DEFAULT CHARSET=utf8 COMMENT='This table will have all the images and videos for a brand. Each campaign will have one or more assets from this.';

/*Table structure for table `brands` */

DROP TABLE IF EXISTS `brands`;

CREATE TABLE `brands` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `brand_name` varchar(64) NOT NULL,
  `brand_desc` varchar(512) NOT NULL,
  `brand_logo_url` text NOT NULL,
  `is_active` tinyint(1) NOT NULL COMMENT '1 - Active\n0 - Inactive',
  `brand_viewed` int(55) DEFAULT NULL,
  `created_dttm` datetime NOT NULL,
  `modified_dttm` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=31 DEFAULT CHARSET=utf8 COMMENT='This is the brand master table';

/*Table structure for table `buy_posts` */

DROP TABLE IF EXISTS `buy_posts`;

CREATE TABLE `buy_posts` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `post_title` varchar(255) DEFAULT NULL,
  `post_desc` varchar(255) DEFAULT NULL,
  `post_banner` varchar(255) DEFAULT NULL,
  `banner_type` int(2) DEFAULT NULL COMMENT '1 - Image 2 - Video',
  `is_active` int(2) DEFAULT NULL,
  `publish_date` datetime DEFAULT NULL,
  `end_date` datetime DEFAULT NULL,
  `created_dttm` datetime DEFAULT NULL,
  `modified_dttm` datetime DEFAULT NULL,
  `buy_now_status` int(2) DEFAULT NULL,
  `buy_now_url` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

/*Table structure for table `camp_behaviour` */

DROP TABLE IF EXISTS `camp_behaviour`;

CREATE TABLE `camp_behaviour` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `targetaudience_id` int(11) NOT NULL,
  `campaign_id` int(11) NOT NULL,
  `camp_behaviour` varchar(256) NOT NULL COMMENT '1.Added A Review,2.Did not Add A Review,3.Did Not Scan QR Code At Vending Machine,4.Obtained Sample QR Code,5.Did Not Obtain Sample QR Code,6.Scanned QR Code At Vending Machine',
  `is_active` tinyint(1) NOT NULL COMMENT '1 - Active 0 - Inactive',
  `created_dttm` datetime NOT NULL,
  `modified_dttm` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `campaign_banners` */

DROP TABLE IF EXISTS `campaign_banners`;

CREATE TABLE `campaign_banners` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `campaign_id` int(11) NOT NULL,
  `banner_url` varchar(256) NOT NULL,
  `banner_type` tinyint(2) NOT NULL COMMENT '1 - Image\n2 - Video',
  `cover_image` tinyint(1) NOT NULL COMMENT '1 - yes 0 - no',
  `sort_order` smallint(3) NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1' COMMENT '1 - Active\n0 - Inactive',
  `created_dttm` datetime NOT NULL,
  `modified_dttm` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_campaignBanners_campaignid_idx` (`campaign_id`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8 COMMENT='This table will have all the banner images/videos for the campaign';

/*Table structure for table `campaign_brand_assets` */

DROP TABLE IF EXISTS `campaign_brand_assets`;

CREATE TABLE `campaign_brand_assets` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `campaign_id` int(11) DEFAULT NULL,
  `brand_id` int(11) DEFAULT NULL,
  `asset_id` int(11) DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL COMMENT '1 - Active\n0 - Inactive',
  `created_dttm` datetime NOT NULL,
  `modified_dttm` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_campaignBrandAssets_campaignid_idx` (`campaign_id`),
  KEY `fk_campaignBrandAssets_brandid_idx` (`brand_id`),
  KEY `fk_campaignBrandAssets_assetid_idx` (`asset_id`),
  CONSTRAINT `fk_campaignBrandAssets_assetid` FOREIGN KEY (`asset_id`) REFERENCES `brand_assets` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_campaignBrandAssets_brandid` FOREIGN KEY (`brand_id`) REFERENCES `brands` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_campaignBrandAssets_campaignid` FOREIGN KEY (`campaign_id`) REFERENCES `campaigns` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8 COMMENT='This table will store the specific brand assets (images, videos) to be shown for a campaign';

/*Table structure for table `campaign_samples` */

DROP TABLE IF EXISTS `campaign_samples`;

CREATE TABLE `campaign_samples` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `campaign_id` int(11) NOT NULL,
  `start_date` datetime NOT NULL,
  `end_date` datetime NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_dttm` datetime NOT NULL,
  `modified_dttm` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=23 DEFAULT CHARSET=utf8;

/*Table structure for table `campaign_samples_location` */

DROP TABLE IF EXISTS `campaign_samples_location`;

CREATE TABLE `campaign_samples_location` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `campaign_samples_id` int(11) DEFAULT NULL,
  `campaign_id` int(11) DEFAULT NULL,
  `location_name` varchar(300) DEFAULT NULL,
  `url` varchar(765) DEFAULT NULL,
  `location_image` varchar(765) DEFAULT NULL,
  `is_active` int(11) DEFAULT NULL,
  `created_dttm` datetime DEFAULT NULL,
  `modified_dttm` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

/*Table structure for table `campaign_vends` */

DROP TABLE IF EXISTS `campaign_vends`;

CREATE TABLE `campaign_vends` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `campaign_id` int(11) DEFAULT NULL,
  `vend_machine_id` int(11) NOT NULL,
  `vend_no_of_available_sample` int(11) NOT NULL DEFAULT '0',
  `vend_no_of_samples` int(11) NOT NULL DEFAULT '0',
  `vend_no_of_sample_used` int(11) NOT NULL DEFAULT '0',
  `is_active` tinyint(1) NOT NULL DEFAULT '1' COMMENT '1 - Active\n0 - Inactive',
  `created_dttm` datetime NOT NULL,
  `modified_dttm` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_campaignvends_campaignid_idx` (`campaign_id`),
  KEY `fk_campaignvends_vendmachineid_idx` (`vend_machine_id`),
  CONSTRAINT `fk_campaignvends_campaignid` FOREIGN KEY (`campaign_id`) REFERENCES `campaigns` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8 COMMENT='This table will provide the specific vending machines for a particular campaign and the total number of samples sanctioned for the campaign';

/*Table structure for table `campaigns` */

DROP TABLE IF EXISTS `campaigns`;

CREATE TABLE `campaigns` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `campaign_code` varchar(50) DEFAULT NULL,
  `campaign_name` varchar(64) NOT NULL,
  `campaign_desc` varchar(500) NOT NULL,
  `brand_id` int(11) DEFAULT NULL,
  `review_id` int(11) DEFAULT NULL,
  `campaign_viewed` int(55) DEFAULT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `total_campaign_samples` int(11) NOT NULL,
  `total_campaign_samples_used` int(11) NOT NULL,
  `avg_rating` float(4,1) NOT NULL,
  `campaign_status` tinyint(2) NOT NULL COMMENT '0 - New (not published)\n1 - Active\n2 - Expired\n3 - De-activated\n',
  `sort_order` smallint(3) NOT NULL,
  `is_publish` tinyint(1) NOT NULL COMMENT '1 - Published 0 - Not Published',
  `buy_now_link` varchar(255) DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL COMMENT '1 - Active\n0 - Inactive',
  `created_dttm` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `modified_dttm` datetime NOT NULL,
  `buy_now_click_total` int(55) DEFAULT '0',
  PRIMARY KEY (`id`,`is_active`),
  KEY `fk_campaigns_brandid_idx` (`brand_id`),
  KEY `fk_campaigns_reviewid_idx` (`review_id`),
  CONSTRAINT `fk_campaigns_brandid` FOREIGN KEY (`brand_id`) REFERENCES `brands` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_campaigns_reviewid_idx` FOREIGN KEY (`review_id`) REFERENCES `reviews` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=50 DEFAULT CHARSET=utf8 COMMENT='This is the main table for all the campaigns. It will have few other satellite tables to show the complete campaign details.';

/*Table structure for table `cities` */

DROP TABLE IF EXISTS `cities`;

CREATE TABLE `cities` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `city_name` varchar(30) NOT NULL,
  `state_id` int(11) NOT NULL,
  `country_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1001 DEFAULT CHARSET=latin1;

/*Table structure for table `city_old` */

DROP TABLE IF EXISTS `city_old`;

CREATE TABLE `city_old` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `city_name` varchar(255) NOT NULL,
  `state_id` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

/*Table structure for table `contacts` */

DROP TABLE IF EXISTS `contacts`;

CREATE TABLE `contacts` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `user_name` varchar(255) NOT NULL,
  `user_email` varchar(255) NOT NULL,
  `comment` text NOT NULL,
  `is_active` int(1) NOT NULL DEFAULT '1',
  `created_dttm` datetime NOT NULL,
  `modified_dttm` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8;

/*Table structure for table `countries` */

DROP TABLE IF EXISTS `countries`;

CREATE TABLE `countries` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `sortname` varchar(3) NOT NULL,
  `country_name` varchar(150) NOT NULL,
  `phonecode` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=247 DEFAULT CHARSET=utf8;

/*Table structure for table `countries_old` */

DROP TABLE IF EXISTS `countries_old`;

CREATE TABLE `countries_old` (
  `id` int(200) NOT NULL AUTO_INCREMENT,
  `country` varchar(100) DEFAULT NULL,
  `status` tinyint(10) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

/*Table structure for table `interest_masters` */

DROP TABLE IF EXISTS `interest_masters`;

CREATE TABLE `interest_masters` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `interest_title` varchar(64) NOT NULL,
  `interest_type` int(1) NOT NULL COMMENT '1 - MCQ\n2 - Yes/No',
  `interest_options` varchar(1024) NOT NULL COMMENT 'To be stored as JSON or delimited options',
  `sort_order` smallint(3) NOT NULL,
  `is_active` tinyint(1) NOT NULL COMMENT '1 - Active\n0 - Inactive',
  `created_dttm` datetime NOT NULL,
  `modified_dttm` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=18 DEFAULT CHARSET=utf8 COMMENT='This is the interest master to be used for building a user’s profile';

/*Table structure for table `interest_options` */

DROP TABLE IF EXISTS `interest_options`;

CREATE TABLE `interest_options` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `interest_id` int(11) DEFAULT NULL,
  `option_text` varchar(256) NOT NULL,
  `option_order` smallint(3) NOT NULL,
  `is_active` tinyint(1) NOT NULL COMMENT '1 - Active\n0 - Inactive',
  `created_dttm` datetime NOT NULL,
  `modified_dttm` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_interestOptions_interestid_idx` (`interest_id`),
  CONSTRAINT `fk_interestOptions_interestid` FOREIGN KEY (`interest_id`) REFERENCES `interest_masters` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=103 DEFAULT CHARSET=utf8 COMMENT='This table will have the answer options for each interest';

/*Table structure for table `notifications` */

DROP TABLE IF EXISTS `notifications`;

CREATE TABLE `notifications` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `noti_type` int(2) NOT NULL COMMENT '1=>post like,2=>post comment,3=>new post,4=>new compaign launched,5=>QR code verification,6=>Sample availability expaired,7=>Rate & review,8=>QR code  Machin Error',
  `user_from_id` int(11) NOT NULL,
  `user_to_id` int(11) NOT NULL,
  `campaign_id` int(11) NOT NULL,
  `post_id` int(11) NOT NULL,
  `msg` text NOT NULL,
  `is_view` int(1) NOT NULL DEFAULT '0',
  `is_active` int(1) NOT NULL DEFAULT '1',
  `created_dttm` datetime NOT NULL,
  `modified_dttm` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=68 DEFAULT CHARSET=utf8;

/*Table structure for table `post_behaviour` */

DROP TABLE IF EXISTS `post_behaviour`;

CREATE TABLE `post_behaviour` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `targetaudience_id` int(11) NOT NULL,
  `post_id` int(11) NOT NULL,
  `post_behaviour` varchar(256) NOT NULL COMMENT '1.Liked A Post,2.Did not Like A Post,3.Obtained Promo Code,4.Commented On A Post,5.Did Not Comment On A Post,6.Did Not Obtain Promo Code',
  `is_active` tinyint(1) NOT NULL COMMENT '1 - Active 0 - Inactive',
  `created_dttm` datetime NOT NULL,
  `modified_dttm` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `review_answer_options` */

DROP TABLE IF EXISTS `review_answer_options`;

CREATE TABLE `review_answer_options` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `review_id` int(11) NOT NULL DEFAULT '0',
  `question_id` int(11) NOT NULL,
  `ans_order` smallint(2) NOT NULL,
  `answer_text` varchar(256) NOT NULL,
  `is_correct` tinyint(1) NOT NULL COMMENT '1 - Correct\n0 - Incorrect',
  `is_active` tinyint(1) NOT NULL COMMENT '1 - Active\n0 - Inactive',
  `created_dttm` datetime NOT NULL,
  `modified_dttm` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_campaignReviewAnswers_questionid_idx` (`question_id`)
) ENGINE=InnoDB AUTO_INCREMENT=201 DEFAULT CHARSET=utf8 COMMENT='This table stores the answer options for each question of a campaign review. It will optionally indicate the correct option, though it may not be required for most questions';

/*Table structure for table `review_questions` */

DROP TABLE IF EXISTS `review_questions`;

CREATE TABLE `review_questions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `review_id` int(11) DEFAULT NULL,
  `ques_order` smallint(2) NOT NULL,
  `ques_text` varchar(256) NOT NULL,
  `ques_type` tinyint(2) NOT NULL COMMENT '1 - MCQ\n2 - Yes/No',
  `is_active` tinyint(1) NOT NULL COMMENT '1 - Active\n0 - Inactive',
  `created_dttm` datetime NOT NULL,
  `modified_dttm` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_reviewQuestions_reviewid_idx` (`review_id`),
  CONSTRAINT `fk_reviewQuestions_reviewid` FOREIGN KEY (`review_id`) REFERENCES `reviews` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=54 DEFAULT CHARSET=utf8 COMMENT='This table will have the questions (with question type) for the reviews of a campaign / post';

/*Table structure for table `reviews` */

DROP TABLE IF EXISTS `reviews`;

CREATE TABLE `reviews` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `review_type` tinyint(2) NOT NULL COMMENT '1 - Campaign Review\n2 - Post Review',
  `is_active` tinyint(1) NOT NULL COMMENT '1 - Active\n0 - Inactive',
  `created_dttm` datetime NOT NULL,
  `modified_dttm` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=45 DEFAULT CHARSET=utf8 COMMENT='This is a master table for storing all the reviews created either for a campaign or a wall post. This table will be a FK to either campaigns table or wallPosts. The questions and answer options are stored separately on two other tables.';

/*Table structure for table `state_old` */

DROP TABLE IF EXISTS `state_old`;

CREATE TABLE `state_old` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `state_name` varchar(50) NOT NULL,
  `state_code` varchar(10) NOT NULL,
  `status` enum('0','1') NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

/*Table structure for table `states` */

DROP TABLE IF EXISTS `states`;

CREATE TABLE `states` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(30) NOT NULL,
  `country_id` int(11) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4122 DEFAULT CHARSET=latin1;

/*Table structure for table `target_audience` */

DROP TABLE IF EXISTS `target_audience`;

CREATE TABLE `target_audience` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `campaign_id` int(11) NOT NULL,
  `brand_id` int(11) NOT NULL,
  `gender` varchar(50) NOT NULL,
  `age` varchar(50) NOT NULL,
  `interest_ques_id` text NOT NULL,
  `interests` text NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_dttm` datetime NOT NULL,
  `modified_dttm` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

/*Table structure for table `user_campaign_interests` */

DROP TABLE IF EXISTS `user_campaign_interests`;

CREATE TABLE `user_campaign_interests` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `campaign_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_dttm` datetime NOT NULL,
  `modified_dttm` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `user_device_details` */

DROP TABLE IF EXISTS `user_device_details`;

CREATE TABLE `user_device_details` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `device_id` varchar(300) NOT NULL,
  `device_token` varchar(1500) NOT NULL,
  `device_type` varchar(300) NOT NULL,
  `device_model` varchar(300) NOT NULL,
  `device_name` varchar(300) NOT NULL,
  `device_os_version` varchar(300) NOT NULL,
  `app_version` varchar(300) NOT NULL,
  `created_dttm` datetime NOT NULL,
  `modified_dttm` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8;

/*Table structure for table `user_interest_options` */

DROP TABLE IF EXISTS `user_interest_options`;

CREATE TABLE `user_interest_options` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL,
  `interest_id` int(11) DEFAULT NULL,
  `option_id` int(11) DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL COMMENT '1 - Active\n0 - Inactive',
  `created_dttm` datetime NOT NULL,
  `modified_dttm` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_userInterestOptions_userid_idx` (`user_id`),
  KEY `fk_userInterestOptions_interestid_idx` (`interest_id`),
  KEY `fk_userInterestOptions_optionid_idx` (`option_id`),
  CONSTRAINT `fk_userInterestOptions_interestid` FOREIGN KEY (`interest_id`) REFERENCES `interest_masters` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_userInterestOptions_optionid` FOREIGN KEY (`option_id`) REFERENCES `interest_options` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_userInterestOptions_userid` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `user_interests` */

DROP TABLE IF EXISTS `user_interests`;

CREATE TABLE `user_interests` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL,
  `interest_id` int(11) DEFAULT NULL,
  `interest_text` varchar(128) NOT NULL,
  `is_active` tinyint(1) NOT NULL COMMENT '1 - Active\n0 - Inactive',
  `created_dttm` datetime NOT NULL,
  `modified_dttm` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_userinterests_userid_idx` (`user_id`),
  KEY `fk_userinterests_interestid_idx` (`interest_id`),
  CONSTRAINT `fk_userinterests_interestid` FOREIGN KEY (`interest_id`) REFERENCES `interest_masters` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_userinterests_userid` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='This table will store the user’s interests as selected in the profile';

/*Table structure for table `user_promocodes` */

DROP TABLE IF EXISTS `user_promocodes`;

CREATE TABLE `user_promocodes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `campaign_id` int(11) NOT NULL,
  `post_id` int(11) NOT NULL,
  `qr_code_url` varchar(256) NOT NULL,
  `unlocked_date` datetime NOT NULL,
  `end_date` date NOT NULL,
  `is_active` int(11) NOT NULL,
  `status` tinyint(2) NOT NULL COMMENT '1:not deleted,2:deleted,3:Used,4:used and deleted',
  `created_dttm` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `modified_dttm` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;

/*Table structure for table `user_review_answers` */

DROP TABLE IF EXISTS `user_review_answers`;

CREATE TABLE `user_review_answers` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL,
  `review_id` int(11) DEFAULT NULL,
  `question_id` int(11) DEFAULT NULL,
  `answer_id` int(11) DEFAULT NULL,
  `answer_text` varchar(256) NOT NULL,
  `is_active` tinyint(1) NOT NULL COMMENT '1 - Active\n0 - Inactive',
  `created_dttm` datetime NOT NULL,
  `modified_dttm` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_userCampaignReviewAnswers_userid_idx` (`user_id`),
  KEY `fk_userCampaignReviewAnswers_questionid_idx` (`question_id`),
  KEY `fk_userCampaignReviewAnswers_answerid_idx` (`answer_id`),
  CONSTRAINT `fk_userReviewAnswers_answerid` FOREIGN KEY (`answer_id`) REFERENCES `review_answer_options` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_userReviewAnswers_questionid` FOREIGN KEY (`question_id`) REFERENCES `review_questions` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_userReviewAnswers_userid` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=78 DEFAULT CHARSET=utf8 COMMENT='This table will have the answers provided by the users for the campaign or post review questions';

/*Table structure for table `user_reviews` */

DROP TABLE IF EXISTS `user_reviews`;

CREATE TABLE `user_reviews` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `review_id` int(11) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `is_campaign_review` int(1) NOT NULL DEFAULT '0',
  `review_text` varchar(512) NOT NULL,
  `rating` float(4,1) NOT NULL,
  `is_published` tinyint(2) NOT NULL DEFAULT '1' COMMENT '1 - Published\n0 - Not Published',
  `is_active` tinyint(1) NOT NULL COMMENT '1 - Active\n0 - Inactive',
  `created_dttm` datetime NOT NULL,
  `modified_dttm` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_campaignReviews_userid_idx` (`user_id`),
  KEY `fk_ userReviews_reviewid_idx` (`review_id`),
  CONSTRAINT `fk_ userReviews_reviewid` FOREIGN KEY (`review_id`) REFERENCES `reviews` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_ userReviews_userid` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8 COMMENT='This table will have the review and rating provided by a user for a campaign or a post. The answers provided by the users will be stored separately';

/*Table structure for table `user_samples` */

DROP TABLE IF EXISTS `user_samples`;

CREATE TABLE `user_samples` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `campaign_sample_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `campaign_id` int(11) DEFAULT NULL,
  `qr_code` text NOT NULL,
  `qr_code_url` varchar(256) NOT NULL,
  `unlocked_date` datetime NOT NULL,
  `authorised_code` text NOT NULL,
  `is_active` tinyint(1) NOT NULL COMMENT '1 - Active\n0 - Inactive',
  `status` tinyint(2) NOT NULL DEFAULT '1' COMMENT '1:not deleted,2:deleted,3:Used,4:used and deleted',
  `qr_code_status` tinyint(2) NOT NULL DEFAULT '0' COMMENT '1:not deleted,2:deleted,3:Used,4:used and deleted',
  `created_dttm` datetime NOT NULL,
  `modified_dttm` datetime NOT NULL,
  `is_user_consent` int(1) DEFAULT NULL COMMENT '1:yes,0:no',
  PRIMARY KEY (`id`),
  KEY `fk_userSampleCodes_userid_idx` (`user_id`),
  KEY `fk_userSampleCodes_campaignid_idx` (`campaign_id`),
  CONSTRAINT `fk_userSamples_campaignid` FOREIGN KEY (`campaign_id`) REFERENCES `campaigns` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_userSamples_userid` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=37 DEFAULT CHARSET=utf8 COMMENT='This table will store all the campaigns sampled by specific users. The table would also have the qr-code in case the user unlocks it';

/*Table structure for table `users` */

DROP TABLE IF EXISTS `users`;

CREATE TABLE `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(64) NOT NULL,
  `email` varchar(64) NOT NULL,
  `phone` varchar(16) NOT NULL,
  `is_pass_forgot` smallint(1) DEFAULT '0' COMMENT '0=> not change,1=>changed',
  `password` text NOT NULL,
  `image` varchar(255) NOT NULL,
  `gender` int(2) NOT NULL DEFAULT '0' COMMENT '1=>''Male'',2=>''Female''',
  `age_bracket_id` int(11) NOT NULL,
  `social_oath_token` varchar(64) NOT NULL,
  `fb_id` varchar(50) DEFAULT NULL,
  `chtbot_otp_count` smallint(1) DEFAULT '0',
  `otp_chatbot` smallint(4) DEFAULT NULL,
  `social_login` tinyint(2) NOT NULL COMMENT '1 - Facebook\n2 - Google',
  `last_login` datetime NOT NULL,
  `last_ip_address` varchar(16) NOT NULL,
  `otp_phone` int(4) DEFAULT NULL,
  `otp` smallint(4) NOT NULL,
  `registration_status` tinyint(3) NOT NULL COMMENT '1 - Registered, OTP not verified\n2 - Registered, OTP verified\n3 - De-activated',
  `last_password_dttm` datetime NOT NULL,
  `is_active` tinyint(1) NOT NULL COMMENT '1 - Active\n0 - Inactive',
  `city_id` int(11) DEFAULT NULL,
  `country_id` int(11) DEFAULT NULL,
  `sms_sample_wrong_count` tinyint(1) DEFAULT '0',
  `is_sms_sample_use` tinyint(1) DEFAULT '0' COMMENT '0=>not use,1=>use',
  `created_dttm` datetime NOT NULL,
  `modified_dttm` datetime NOT NULL,
  `profile_completion` int(11) DEFAULT '10',
  `imei` bigint(20) DEFAULT NULL,
  `smsqr_code_url` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`,`otp`),
  KEY `fk_users_agebracket_idx` (`age_bracket_id`)
) ENGINE=InnoDB AUTO_INCREMENT=18 DEFAULT CHARSET=utf8 COMMENT='This table will have all the users’ login/registration details ';

/*Table structure for table `vending_machines` */

DROP TABLE IF EXISTS `vending_machines`;

CREATE TABLE `vending_machines` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `vending_machine_code` varchar(255) DEFAULT NULL,
  `location_name` varchar(64) DEFAULT NULL,
  `location_address` varchar(256) DEFAULT NULL,
  `location_address3` varchar(256) DEFAULT NULL,
  `location_address2` varchar(256) DEFAULT NULL,
  `country` int(11) DEFAULT NULL,
  `state` int(11) DEFAULT NULL,
  `city` int(11) DEFAULT NULL,
  `postal_code` int(11) DEFAULT NULL,
  `landmark` varchar(256) DEFAULT NULL,
  `vend_lat` double DEFAULT NULL,
  `vend_long` double DEFAULT NULL,
  `vend_no_of_available_sample` int(11) NOT NULL,
  `vend_no_of_sample_used` int(11) NOT NULL,
  `is_active` tinyint(1) DEFAULT NULL COMMENT '1 - Active\n0 - Inactive',
  `created_dttm` datetime DEFAULT NULL,
  `modified_dttm` datetime DEFAULT NULL,
  `invalid_try` int(11) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=51 DEFAULT CHARSET=utf8 COMMENT='This is a master table for all the participating vending machines across all campaigns';

/*Table structure for table `wall_comments` */

DROP TABLE IF EXISTS `wall_comments`;

CREATE TABLE `wall_comments` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `post_id` int(11) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `comments` text NOT NULL,
  `is_active` tinyint(1) NOT NULL COMMENT '1 - Active\n0 - Inactive',
  `created_dttm` datetime NOT NULL,
  `modified_dttm` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_wallComments_postid_idx` (`post_id`),
  KEY `fk_wallComments_userid_idx` (`user_id`),
  CONSTRAINT `fk_wallComments_postid` FOREIGN KEY (`post_id`) REFERENCES `wall_posts` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COMMENT='This table will have the comments for the posts by the users';

/*Table structure for table `wall_likes` */

DROP TABLE IF EXISTS `wall_likes`;

CREATE TABLE `wall_likes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `post_id` int(11) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL COMMENT '1 - Active\n0 - Inactive',
  `created_dttm` datetime NOT NULL,
  `modified_dttm` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_wallLikes_postid_idx` (`post_id`),
  KEY `fk_wallLikes_userid_idx` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='This table will have the likes by each user for a post';

/*Table structure for table `wall_posts` */

DROP TABLE IF EXISTS `wall_posts`;

CREATE TABLE `wall_posts` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `post_title` varchar(64) NOT NULL,
  `post_desc` varchar(512) NOT NULL,
  `campaign_id` int(11) DEFAULT NULL,
  `brand_id` int(11) DEFAULT NULL,
  `review_id` int(11) DEFAULT NULL,
  `post_banner_url` varchar(256) NOT NULL,
  `banner_type` int(2) NOT NULL COMMENT '1 - Image\n2 - Video',
  `has_promo` int(1) NOT NULL COMMENT '1 - Yes\n0 - No',
  `coupon_text` varchar(50) NOT NULL,
  `qr_code_url` varchar(255) NOT NULL,
  `promo_desc` varchar(255) NOT NULL,
  `promo_end_date` date NOT NULL,
  `no_of_likes` int(11) NOT NULL,
  `no_of_comments` int(11) NOT NULL,
  `avg_rating` float(4,1) NOT NULL,
  `is_active` tinyint(1) NOT NULL COMMENT '1 - Active\n0 - Inactive',
  `is_publish` tinyint(1) NOT NULL,
  `publish_date` date NOT NULL,
  `created_dttm` datetime NOT NULL,
  `modified_dttm` datetime NOT NULL,
  `buy_now_status` int(1) DEFAULT '0' COMMENT '0- No, 1-Yes',
  `buy_now_url` varchar(255) DEFAULT NULL,
  `buy_now_click_total` int(55) DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `fk_wallPosts_brandid_idx` (`brand_id`),
  KEY `fk_wallPosts_reviewid_idx` (`review_id`),
  KEY `fk_wallPosts_campaignid_idx` (`campaign_id`),
  CONSTRAINT `fk_wall_posts_brandid` FOREIGN KEY (`brand_id`) REFERENCES `brands` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_wall_posts_campaignid` FOREIGN KEY (`campaign_id`) REFERENCES `campaigns` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_wall_posts_reviewid` FOREIGN KEY (`review_id`) REFERENCES `reviews` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=42 DEFAULT CHARSET=utf8;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
