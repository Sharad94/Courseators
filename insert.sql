create table course (courseid varchar(6) primary key, name text not null, description text, ltp text, credits float);
insert into course values ('EPL338','Non-linear Phenomena in Physics and Engineering','No Descrioption Available','(3-1-0)',4);
insert into course values ('TTL222','Yarn Manufacture - II','No Descrioption Available','(3-1-0)',4);
insert into course values ('TTP221','Yarn Manufacture Laboratory - I','No Descrioption Available','(0-0-2)',1);
insert into course values ('CHD771','Minor Project','No Descrioption Available','(0-0-8)',4);
insert into course values ('CHL732','Soft Lithographic Methods for Nano-Fabrication','No Descrioption Available','(3-0-0)',3);
insert into course values ('CHL740','Special Topics','No Descrioption Available','(3-0-0)',3);
insert into course values ('CHP768','Fundamentals of Computational Fluid Dynamics','No Descrioption Available','(2-0-2)',3);
insert into course values ('MAD853','Major Project Part 1 (MT)','No Descrioption Available','(0-0-8)',4);
insert into course values ('MAD854','Major Project Part 2 (MT)','No Descrioption Available','(0-0-32)',16);
insert into course values ('MAD350','Mini Project (MT)','No Descrioption Available','(0-0-6)',3);
insert into course values ('SML410','Computational Techniques for Management Applications','No Descrioption Available','(3-0-2)',4);

--courseid2 is prerequisite of courseid1--
create table prereq (courseid1 varchar(6) references course(courseid) on delete cascade on update cascade, prereq text not null,primary key(courseid1,prereq));

--both overlap always use left column only--
create table overlap (courseid1 varchar(6) references course(courseid) on delete cascade on update cascade, courseid2 varchar(6) references course(courseid) on delete cascade on update cascade, primary key(courseid1,courseid2));

create table student (studentid text primary key, password text not null, name text not null, img text, year int not null, email text not null, advisor text, telno text, mobile text, fax text, address text, homephone text, roomaddr text, postaladdr text,url text);

create table prof (profid text primary key, password text not null, name text not null, img text, email text not null, advisor text, telno text, mobile text, fax text, address text, homephone text, roomaddr text, postaladdr text,url text);

create table department (depid varchar(3) primary key, name text not null, corecred int, eleccred int);

create table coreof (courseid varchar(6) references course(courseid) on delete cascade on update cascade, depid varchar(3) references department(depid) on delete cascade on update cascade, primary key(courseid, depid));

create table elecof (courseid varchar(6) references course(courseid) on delete cascade on update cascade, depid varchar(3) references department(depid) on delete cascade on update cascade, primary key(courseid, depid));

create table studentdep (studentid text primary key references student(studentid) on delete cascade on update cascade, depid varchar(3) references department(depid) on delete cascade on update cascade);

--primary key baanani hai , grading and rating out of 10.
create table reviews (reviewid serial primary key, profid text, attpolicy int, rating int, grading int, comments text);
--Bharni Hain--
create table semester (year int, semnumber int, primary key(year, semnumber));

create table donecourses (studentid text references student(studentid) on delete cascade on update cascade, year int, semnumber int, courseid text references course(courseid) on delete cascade on update cascade, reviewid int references reviews(reviewid) on delete cascade on update cascade, foreign key (year,semnumber) references semester(year,semnumber) on delete cascade on update cascade, primary key(studentid,courseid));

create table tentativecourses (studentid text references student(studentid) on delete cascade on update cascade, courseid text references course(courseid) on delete cascade on update cascade,depid varchar(3) references department(depid) on delete cascade on update cascade, primary key(studentid,courseid));

create table questions (questionid serial primary key, question text not null, questiontime text not null);

create table asks (questionid serial primary key references questions(questionid) on delete cascade on update cascade, courseid varchar(6) references course(courseid) on delete cascade on update cascade, studentid text references student(studentid) on delete cascade on update cascade);

create table answers (upvotes int not null, downvotes int not null, questionid int references questions(questionid) on delete cascade on update cascade, answer text not null, studentid text references student(studentid) on delete cascade on update cascade, primary key (questionid, studentid));


create table checkupvotes(studentid1 text references student(studentid) on delete cascade on update cascade, studentid2 text references student(studentid) on delete cascade on update cascade, questionid int references questions(questionid) on delete cascade on update cascade, primary key (studentid1, studentid2, questionid));


create table checkdownvotes(studentid1 text references student(studentid) on delete cascade on update cascade, studentid2 text references student(studentid) on delete cascade on update cascade, questionid int references questions(questionid) on delete cascade on update cascade, primary key (studentid1, studentid2, questionid));


--Bharni Hain--
create table slot (slotid varchar(2) primary key, starttime text , endtime text);

create table coursesem (courseid varchar(6) references course(courseid) on delete cascade on update cascade, profid text references prof(profid) on delete cascade on update cascade, slotid varchar(2) references slot(slotid) on delete cascade on update cascade, studentsregistered int, year int, semnumber int, primary key(courseid,year,semnumber));

create table bookmarks (studentid text references student(studentid) on delete cascade on update cascade, courseid varchar(6) references course(courseid) on delete cascade on update cascade, primary key(studentid,courseid));

create table tentativefriends (studentid1 text references student(studentid) on delete cascade on update cascade, studentid2 text references student(studentid) on delete cascade on update cascade, primary key(studentid1, studentid2));

create table friends (studentid1 text references student(studentid) on delete cascade on update cascade, studentid2 text references student(studentid) on delete cascade on update cascade, primary key(studentid1, studentid2));	

create table conveners (studentid text references student(studentid) on delete cascade on update cascade, 
	depid varchar(3) references department(depid) on delete cascade on update cascade, primary key(studentid, depid));

grant all privileges on table course to psql;
grant all privileges on table prereq to psql;
grant all privileges on table overlap to psql;
grant all privileges on table student to psql;
grant all privileges on table prof to psql;
grant all privileges on table department to psql;
grant all privileges on table coreof to psql;
grant all privileges on table elecof to psql;
grant all privileges on table studentdep to psql;
grant all privileges on table reviews to psql;
grant all privileges on table semester to psql;
grant all privileges on table donecourses to psql;
grant all privileges on table material to psql;
grant all privileges on table questions to psql;
grant all privileges on table asks to psql;
grant all privileges on table answers to psql;
grant all privileges on table slot to psql;
grant all privileges on table coursesem to psql;
grant all privileges on table bookmarks to psql;
grant all privileges on table tentativecourses to psql;
grant usage , select on questions_questionid_seq to psql;
grant usage , select on asks_questionid_seq to psql;
grant usage , select on	reviews_reviewid_seq to psql;
grant all privileges on table tentativefriends to psql;
grant all privileges on table friends to psql;

create index dep_index on tentativecourses (depid);
