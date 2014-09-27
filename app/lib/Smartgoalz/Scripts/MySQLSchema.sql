/* STORED PROCEDURE */

DELIMITER $$

CREATE PROCEDURE `task_calc_completion` (IN goal_id INT(11))
LANGUAGE SQL
DETERMINISTIC
SQL SECURITY DEFINER
COMMENT 'A procedure to update the task completion status in the goal'
BEGIN
	DECLARE task_total int;
	DECLARE task_completed int;
	DECLARE goal_completed int;
	SELECT COUNT(id) INTO task_total FROM tasks WHERE goal_id = goal_id;
	SELECT COUNT(id) INTO task_completed FROM tasks WHERE goal_id = goal_id AND is_completed = 1;
	IF (task_total <=> task_completed AND task_total != 0) THEN
		SET goal_completed = 1;
	ELSE
		SET goal_completed = 0;
	END IF;
	UPDATE goals SET task_total = task_total, task_completed = task_completed, is_completed = goal_completed WHERE id = goal_id;
END$$

DELIMITER ;

/* TRIGGERS */

DELIMITER $$
CREATE TRIGGER `create_task` AFTER INSERT ON `tasks`
FOR EACH ROW
BEGIN
	CALL task_calc_completion(NEW.goal_id);
END;
$$

CREATE TRIGGER `update_task` AFTER UPDATE ON `tasks`
FOR EACH ROW
BEGIN
	CALL task_calc_completion(NEW.goal_id);
END;
$$

CREATE TRIGGER `delete_task` AFTER DELETE ON `tasks`
FOR EACH ROW
BEGIN
	CALL task_calc_completion(OLD.goal_id);
END;
$$

CREATE TRIGGER `create_timewatches` BEFORE INSERT ON `timewatches`
FOR EACH ROW
BEGIN
SET NEW.date = DATE_FORMAT(NEW.start_time, '%Y-%m-%d');
IF NEW.is_active = 0 THEN
	SET NEW.minutes_count = TIMESTAMPDIFF(MINUTE, NEW.start_time, NEW.stop_time);
	IF (NEW.minutes_count < 0) THEN
		SIGNAL SQLSTATE '12345' SET MESSAGE_TEXT = 'End time cannot be before start time';
	END IF;
ELSE
	SET NEW.minutes_count = 0;
	SET NEW.stop_time = NULL;
END IF;
END;
$$

CREATE TRIGGER `update_timewatches` BEFORE UPDATE ON `timewatches`
FOR EACH ROW
BEGIN

DECLARE local_starttime timestamp;
DECLARE local_stoptime timestamp;

IF (NEW.start_time IS NULL) THEN
	SET local_starttime = OLD.start_time;
ELSE
	SET NEW.date = DATE_FORMAT(NEW.start_time, '%Y-%m-%d');
	SET local_starttime = NEW.start_time;
END IF;

IF (NEW.stop_time IS NULL) THEN
	SET local_stoptime = OLD.stop_time;
ELSE
	SET local_stoptime = NEW.stop_time;
END IF;

IF NEW.is_active = 0 THEN
	SET NEW.minutes_count = TIMESTAMPDIFF(MINUTE, local_starttime, local_stoptime);

	IF (NEW.minutes_count < 0) THEN
		SIGNAL SQLSTATE '12345' SET MESSAGE_TEXT = 'End time cannot be before start time';
	END IF;
ELSE
	SET NEW.minutes_count = 0;
	SET NEW.stop_time = NULL;
END IF;
END;
$$

DELIMITER ;

/* VIEWS */
