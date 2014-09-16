CREATE OR REPLACE FUNCTION process_coursesem() RETURNS TRIGGER AS $coursesem$
    BEGIN
        IF (TG_OP = 'DELETE') THEN
            UPDATE coursesem SET studentsregistered = studentsregistered-1 where courseid = OLD.courseid and year=2014 and semnumber=1;
            RETURN OLD;
        ELSIF (TG_OP = 'INSERT') THEN
            UPDATE coursesem SET studentsregistered = studentsregistered+1 where courseid = NEW.courseid and year=2014 and semnumber=1;
            RETURN NEW;
        END IF;
        RETURN NULL; -- result is ignored since this is an AFTER trigger
    END;
$coursesem$ LANGUAGE plpgsql;

CREATE TRIGGER course_sem
AFTER INSERT OR DELETE ON tentativecourses
    FOR EACH ROW EXECUTE PROCEDURE process_coursesem();