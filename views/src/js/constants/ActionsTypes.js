/*
    REDUX ACTIONS
 */
// Authenticate actions
export const AUTH = "AUTH"
export const AUTH_LOAD = "AUTH_LOAD"
export const LOGOUT = "LOGOUT"
export const CHANGE_PASSWORD = "CHANGE_PASSWORD"
export const SET_PASSWORD = "SET_PASSWORD"
export const FORGOT_PASSWORD = "FORGOT_PASSWORD"
// Public actions
export const LOAD_LECTURERS = "LOAD_LECTURERS"
export const LOAD_DEGREES = "LOAD_DEGREES"
export const LOAD_TRAINING_TYPES = "LOAD_TRAINING_TYPES"
export const LOAD_TRAINING_LEVELS = "LOAD_TRAINING_LEVELS"
export const LOAD_DEPARTMENTS = "LOAD_DEPARTMENTS"
export const LOAD_TRAINING_AREAS = "LOAD_TRAINING_AREAS"
export const LOAD_TRAINING_PROGRAMS = "LOAD_TRAINING_PROGRAMS"
export const LOAD_TRAINING_COURSES = "LOAD_TRAINING_COURSES"
export const LOAD_AREAS = "LOAD_AREAS"
export const LOAD_PUBLIC_LECTURER = "LOAD_PUBLIC_LECTURER"
export const LOAD_PUBLIC_LECTURER_AREAS = "LOAD_PUBLIC_LECTURER_AREAS"
export const LOAD_ANNOUNCEMENTS = "LOAD_ANNOUNCEMENTS"
export const LOAD_OUT_OFFICERS = "LOAD_OUT_OFFICERS"
export const LOAD_ACTIVE_QUOTA = "LOAD_ACTIVE_QUOTA"
// Superuser actions
export const SUPERUSER_LOAD_FACULTIES = "SUPERUSER_LOAD_FACULTIES"
export const SUPERUSER_CREATE_FACULTY = "SUPERUSER_CREATE_FACULTY"
export const SUPERUSER_UPDATE_FACULTY = "SUPERUSER_UPDATE_FACULTY"
export const SUPERUSER_DELETE_FACULTY = "SUPERUSER_DELETE_FACULTY"
export const SUPERUSER_CREATE_DEGREE = "SUPERUSER_CREATE_DEGREE"
export const SUPERUSER_UPDATE_DEGREE = "SUPERUSER_UPDATE_DEGREE"
export const SUPERUSER_DELETE_DEGREE = "SUPERUSER_DELETE_DEGREE"
export const SUPERUSER_CREATE_TRAINING_TYPE = "SUPERUSER_CREATE_TRAINING_TYPE"
export const SUPERUSER_UPDATE_TRAINING_TYPE = "SUPERUSER_UPDATE_TRAINING_TYPE"
export const SUPERUSER_DELETE_TRAINING_TYPE = "SUPERUSER_DELETE_TRAINING_TYPE"
export const SUPERUSER_CREATE_TRAINING_LEVEL = "SUPERUSER_CREATE_TRAINING_LEVEL"
export const SUPERUSER_UPDATE_TRAINING_LEVEL = "SUPERUSER_UPDATE_TRAINING_LEVEL"
export const SUPERUSER_DELETE_TRAINING_LEVEL = "SUPERUSER_DELETE_TRAINING_LEVEL"
export const SUPERUSER_LOAD_QUOTAS = "SUPERUSER_LOAD_QUOTAS"
export const SUPERUSER_CREATE_QUOTA = "SUPERUSER_CREATE_QUOTA"
export const SUPERUSER_UPDATE_QUOTA = "SUPERUSER_UPDATE_QUOTA"
export const SUPERUSER_CHANGE_ACTIVE_QUOTA = "SUPERUSER_CHANGE_ACTIVE_QUOTA"
export const SUPERUSER_DELETE_QUOTA = "SUPERUSER_DELETE_QUOTA"
// Admin actions
export const ADMIN_LOAD_DEPARTMENTS = "ADMIN_LOAD_DEPARTMENTS"
export const ADMIN_CREATE_DEPARTMENT = "ADMIN_CREATE_DEPARTMENT"
export const ADMIN_DELETE_DEPARTMENT = "ADMIN_DELETE_DEPARTMENT"
export const ADMIN_UPDATE_DEPARTMENT = "ADMIN_UPDATE_DEPARTMENT"
export const ADMIN_LOAD_OFFICERS = "ADMIN_LOAD_OFFICERS"
export const ADMIN_CREATE_OFFICER = "ADMIN_CREATE_OFFICER"
export const ADMIN_UPDATE_OFFICER = "ADMIN_UPDATE_OFFICER"
export const ADMIN_DELETE_OFFICER = "ADMIN_DELETE_OFFICER"
export const ADMIN_CREATE_TRAINING_AREA = "ADMIN_CREATE_TRAINING_AREA"
export const ADMIN_UPDATE_TRAINING_AREA = "ADMIN_UPDATE_TRAINING_AREA"
export const ADMIN_DELETE_TRAINING_AREA = "ADMIN_DELETE_TRAINING_AREA"
export const ADMIN_CREATE_TRAINING_PROGRAM = "ADMIN_CREATE_TRAINING_PROGRAM"
export const ADMIN_UPDATE_TRAINING_PROGRAM = "ADMIN_UPDATE_TRAINING_PROGRAM"
export const ADMIN_DELETE_TRAINING_PROGRAM = "ADMIN_DELETE_TRAINING_PROGRAM"
export const ADMIN_CREATE_TRAINING_COURSE = "ADMIN_CREATE_TRAINING_COURSE"
export const ADMIN_UPDATE_TRAINING_COURSE = "ADMIN_UPDATE_TRAINING_COURSE"
export const ADMIN_DELETE_TRAINING_COURSE = "ADMIN_DELETE_TRAINING_COURSE"
export const ADMIN_LOAD_LEARNERS = "ADMIN_LOAD_LEARNERS"
export const ADMIN_CREATE_LEARNER = "ADMIN_CREATE_LEARNER"
export const ADMIN_UPDATE_LEARNER = "ADMIN_UPDATE_LEARNER"
export const ADMIN_DELETE_LEARNER = "ADMIN_DELETE_LEARNER"
export const ADMIN_CREATE_AREA = "ADMIN_CREATE_AREA"
export const ADMIN_DELETE_AREA = "ADMIN_DELETE_AREA"
export const ADMIN_UPDATE_AREA = "ADMIN_UPDATE_AREA"
export const ADMIN_LOAD_TOPICS = "ADMIN_LOAD_TOPICS"
export const ADMIN_TOPIC_IMPORT_LEARNERCODES = "ADMIN_TOPIC_IMPORT_LEARNERCODES"
export const ADMIN_UPDATE_TOPIC = "ADMIN_UPDATE_TOPIC"
export const ADMIN_DELETE_TOPIC = "ADMIN_DELETE_TOPIC"
export const ADMIN_FLUSH_TOPICS = "ADMIN_FLUSH_TOPICS"
export const ADMIN_LOAD_OUT_TOPICS = "ADMIN_LOAD_OUT_TOPICS"
export const ADMIN_FACULTY_REQUEST = "ADMIN_FACULTY_REQUEST"
export const ADMIN_FACULTY_RESPONSE = "ADMIN_FACULTY_RESPONSE"
export const ADMIN_FACULTY_DECLINE = "ADMIN_FACULTY_DECLINE"
export const ADMIN_FACULTY_ALLOW_EDIT = "DMIN_FACULTY_ALLOW_EDIT"
export const ADMIN_CREATE_OUT_OFFICERS = "ADMIN_CREATE_OUT_OFFICERS"
export const ADMIN_LOAD_ANNOUNCEMENTS = "ADMIN_LOAD_ANNOUNCEMENTS"
export const ADMIN_CREATE_ANNOUCEMENT = "ADMIN_CREATE_ANNOUCEMENT"
export const ADMIN_UPDATE_ANNOUCEMENT = "ADMIN_UPDATE_ANNOUCEMENT"
export const ADMIN_DELETE_ANNOUCEMENT = "ADMIN_DELETE_ANNOUCEMENT"
export const ADMIN_OPEN_TOPIC_CHANGE = "ADMIN_OPEN_TOPIC_CHANGE"
export const ADMIN_REQUEST_TOPIC_CHANGE = "ADMIN_REQUEST_TOPIC_CHANGE"
export const ADMIN_REQUEST_TOPIC_PROTECT = "ADMIN_REQUEST_TOPIC_PROTECT"
export const ADMIN_CHECK_PAPER = "ADMIN_CHECK_PAPER"
export const ADMIN_FINISH_TOPIC = "ADMIN_FINISH_TOPIC"
export const ADMIN_LOAD_DOCUMENTS = "ADMIN_LOAD_DOCUMENTS"
export const ADMIN_CREATE_DOCUMENT = "ADMIN_CREATE_DOCUMENT"
export const ADMIN_EDIT_DOCUMENT = "ADMIN_EDIT_DOCUMENT"
export const ADMIN_OPEN_TOPIC_SEMINAR = "ADMIN_OPEN_TOPIC_SEMINAR"
export const ADMIN_CLOSE_TOPIC_SEMINAR = "ADMIN_CLOSE_TOPIC_SEMINAR"
export const ADMIN_CLOSE_REGISTER_SEMINAR = "ADMIN_CLOSE_REGISTER_SEMINAR"
// Lecturer actions
export const LECTURER_LOAD_INFOMATION = "LECTURER_LOAD_INFOMATION"
export const LECTURER_UPDATE_INFOMATION = "LECTURER_UPDATE_INFOMATION"
export const LECTURER_LOAD_AREAS = "LECTURER_LOAD_AREAS"
export const LECTURER_ADD_AREAS = "LECTURER_ADD_AREAS"
export const LECTURER_DELETE_AREA = "LECTURER_DELETE_AREA"
export const LECTURER_LOAD_TOPICS = "LECTURER_LOAD_TOPICS"
export const LECTURER_ACCEPT_TOPIC = "LECTURER_ACCEPT_TOPIC"
export const LECTURER_DECLINE_TOPIC = "LECTURER_DECLINE_TOPIC"
export const LECTURER_EDIT_TOPIC = "LECTURER_EDIT_TOPIC"
export const LECTURER_UPLOAD_AVATAR = "LECTURER_UPLOAD_AVATAR"
export const LECTURER_REMOVE_AVATAR = "LECTURER_REMOVE_AVATAR"
export const LECTURER_UPDATE_REVIEW = "LECTURER_UPDATE_REVIEW"
// Learner actions
export const LEARNER_LOAD_PROFILE = "LEARNER_LOAD_PROFILE"
export const LEARNER_UPDATE_PROFILE = "LEARNER_UPDATE_PROFILE"
export const LEARNER_LOAD_TOPICS = "LEARNER_LOAD_TOPICS"
export const LEARNER_CREATE_TOPIC = "LEARNER_CREATE_TOPIC"
export const LEARNER_CHANGE_TOPIC = "LEARNER_CHANGE_TOPIC"
export const LEARNER_CREATE_OUTOFFICERS = "LEARNER_CREATE_OUTOFFICERS"
export const LEARNER_CANCEL_CHANGE_REQUEST = "LEARNER_CANCEL_CHANGE_REQUEST"
export const LEARNER_UPLOAD_ATTACHMENT = "LEARNER_UPLOAD_ATTACHMENT"
// Department actions
export const DEPARTMENT_LOAD_TOPICS = "DEPARTMENT_LOAD_TOPICS"
export const DEPARTMENT_APPROVE_TOPIC = "DEPARTMENT_APPROVE_TOPIC"
export const DEPARTMENT_ASSIGN_REVIEWER = "DEPARTMENT_ASSIGN_REVIEWER"
export const DEPARTMENT_FLUSH_TOPICS = "DEPARTMENT_FLUSH_TOPICS"
export const DEPARTMENT_LOAD_SEMINAR = "DEPARTMENT_LOAD_SEMINAR"
// Browse lecturers actions
export const LOAD_LECTURERS_OF_DEPARTMENT = "LOAD_LECTURERS_OF_DEPARTMENT"
export const LOAD_LECTURERS_HAS_AREA = "LOAD_LECTURERS_HAS_AREA"

/*
    HANDLE DOM ACTIONS
 */
export const ADMIN_ADD_DOM_AREA = "ADMIN_ADD_DOM_AREA"
export const ADMIN_UPDATE_DOM_AREA = "ADMIN_UPDATE_DOM_AREA"
export const ADMIN_REMOVE_DOM_AREA = "ADMIN_REMOVE_DOM_AREA"
/*
    API
 */
// API for authenticate
export const API_AUTH = "/auth"
export const API_AUTH_LOAD = "/load-auth"
export const API_LOGOUT = "/logout"
export const API_SET_PASSWORD = "/set-password"
export const API_CHANGE_PASSWORD = "/change-password"
export const API_FORGOT_PASSWORD = "/forgot-password"
// API without prefix
export const API_FACULTY = "/faculty"
export const API_DEGREE = "/degree"
export const API_TRAINING_TYPE = "/training-type"
export const API_TRAINING_LEVEL = "/training-level"
export const API_TRAINING_AREA = "/training-area"
export const API_TRAINING_PROGRAM = "/training-program"
export const API_TRAINING_COURSE = "/training-course"
export const API_DEPARTMENT = "/department"
export const API_AREA = "/knowledge-area"
export const API_TOPIC = "/topic"
export const API_TOPIC_ACTIVITY = "/topic/activity"
export const API_OUT_OFFICER = "/out-officer"
export const API_ANNOUNCEMENT = "/announcement"
export const API_QUOTA = "/quota"
export const API_DOCUMENT = "/document"
// API for superuser
export const API_SUPERUSER_FACULTY = "/superuser/faculty"
export const API_SUPERUSER_TRAINING_TYPE = "/superuser/training-type"
export const API_SUPERUSER_TRAINING_LEVEL = "/superuser/training-level"
export const API_SUPERUSER_DEGREE = "/superuser/degree"
export const API_SUPERUSER_QUOTA = "/superuser/quota"
// API for admin
export const API_ADMIN_DEPARTMENT = "/admin/department"
export const API_ADMIN_OFFICER = "/admin/officer"
export const API_ADMIN_IMPORT_OFFICER = "/admin/import-officer"
export const API_ADMIN_TRAINING_AREA = "/admin/training-area"
export const API_ADMIN_TRAINING_PROGRAM = "/admin/training-program"
export const API_ADMIN_TRAINING_COURSE = "/admin/training-course"
export const API_ADMIN_LEARNER = "/admin/learner"
export const API_ADMIN_CREATE_LEARNER = "/admin/import-learner"
export const API_ADMIN_AREA = "/admin/knowledge-area"
export const API_ADMIN_ANNOUNCEMENT = "/admin/announcement"
export const API_ADMIN_TOPIC = "/admin/topic"
export const API_ADMIN_TOPIC_IMPORT_LEARNERCODES = "/admin/topic/import-lc"
export const API_ADMIN_OUT_OFFICER = "/admin/out-officer"
export const API_ADMIN_REQUEST_CHANGE = "/admin/request-change"
export const API_ADMIN_REQUEST_PROTECT = "/admin/request-protect"
export const API_ADMIN_DOCUMENT = "/admin/document"
// API for officer
export const API_OFFICER = "/officer"
export const API_LECTURER_REMOVE_AVATAR = "/officer/remove-avatar"
export const API_LECTURER_UPLOAD_AVATAR = "/officer/upload-avatar"
export const API_OFFICER_KNOWLEDGE_AREA = "/officer/knowledge-area"
export const API_OFFICER_REVIEW = "/officer/review"
// API for learner
export const API_LEARNER = "/learner"
export const API_LEARNER_ATTACHMENT = "/learner/attachment-register"
// API for department
export const API_DEPARTMENT_REVIEWER = "/department/assign-reviewer"

export const UPLOAD_FILE = "UPLOAD_FILE"
export const API_UPLOAD_FILE = "/attachment"
/*
    OTHER CONSTANTS
 */
export const MIN_QUOTA = 1.0 / 3
export const SUPERUSER_ROLE = 0
export const ADMIN_ROLE = 1
export const LEARNER_ROLE = 2
export const OFFICER_LECTURER_ROLE = 3
export const OFFICER_ADMIN_ROLE = 4
export const OUT_OFFICER_ROLE = 5
export const DEPARTMENT_ROLE = 6
