// Minimum duration to seminar (months)
export const SEMINAR_DURATION = 1

// Pagination config
export const ITEM_PER_PAGE = 15
export const PAGE_RANGE = 5

// Route name config
export const routeName = {
    // Public path
    'LOGIN': 'login',
    'SET_PASSWORD': 'set-password',
    'BROWSE_LECTURER': 'browse-lecturer',
    'BROWSE_LECTURERS': 'browse-lecturers',
    'TOPIC': 'topic',
    // Superuser path, have prefix-path define in SUPERUSER
    'SUPERUSER': 'superuser',
        'FACULTIES': 'faculties',
        'DEGREES': 'degrees',
        'TRAININGS': 'trainings',
    // Admin path, have prefix-path define in ADMIN
    'ADMIN': 'admin',
        'DEPARTMENTS': 'departments',
        'OFFICERS': 'officers',
        'TRAINING_AREAS': 'training-areas',
        'TRAINING_PROGRAMS': 'training-programs',
        'TRAINING_COURSES': 'training-courses',
        'TRAINING_TYPES_AND_LEVELS': 'trainings',
        'DEGREES': 'degrees',
        'KNOWLEDGE_AREAS': 'areas',
        'LEARNERS': 'learners',
        'TOPICS': 'topics',
            'TOPIC_OUT_OFFICERS': 'out-officers',
            'ADMIN_TOPIC_STUDENT': 'student',
            'ADMIN_TOPIC_GRADUATED': 'graduated',
            'ADMIN_TOPIC_RESEARCHER': 'researcher',
        'QUOTAS': 'quotas',
        'DOCUMENTS': 'documents',
    // Lecturer path, have prefix-path define in LECTURER
    'LECTURER': 'lecturer',
        'LECTURER_PROFILE': 'profile',
        'LECTURER_TOPIC': 'current-topics',
            'LECTURER_STUDENT': 'student',
            'LECTURER_GRADUATED': 'graduated',
            'LECTURER_RESEARCHER': 'researcher',
        'LECTURER_EXPERTISE': 'expertise-topics',
    // Learner path, have prefix-path define in LEARNER
    'LEARNER': 'learner',
        'LEARNER_PROFILE': 'profile',
        'LEARNER_TOPIC': 'topics',
    // Learner path, have prefix-path define in DEPARTMENT
    'DEPARTMENT': 'department',
        'SEMINAR': 'seminar',
    // Not found
    'NOT_FOUND': 'not-found'
}

// Topic status config
export const TOPIC_STATUS = {
    100: 'Chưa đăng ký',
    101: 'Chờ giảng viên chấp nhận',
    102: 'Chờ xuất trình',
    103: 'Chờ ghi nhận phê duyệt',
    104: 'Chờ Bộ môn thẩm định',
    888: 'Đang thực hiện',

    889: 'Đang thực hiện - được phép chỉnh sửa',
    890: 'Chờ giảng viên chấp nhận chỉnh sửa',
    891: 'Chờ xuất trình chỉnh sửa',
    892: 'Chờ quyết định thay đổi đề tài',
    896: 'Chờ Bộ môn phê duyệt chỉnh sửa',

    893: 'Chờ giảng viên chấp nhận xin gia hạn',
    894: 'Chờ xuất trình xin gia hạn',
    895: 'Chờ quyết định gia hạn',

    200: 'Chờ giảng viên chấp nhận xin thôi',
    201: 'Chờ xuất trình xin thôi',
    202: 'Chờ quyết định dừng đề tài',

    300: 'Chờ giảng viên chấp nhận xin tạm hoãn',
    301: 'Chờ xuất trình xin tạm hoãn',
    302: 'Chờ quyết định tạm hoãn đề tài',
    303: 'Đang tạm hoãn',
    887: 'Đang thực hiện và chờ hoãn',

    897: 'Chưa đăng ký seminar',
    898: 'Chờ giảng viên chấp nhận đăng ký seminar',
    899: 'Chờ báo cáo seminar',
    900: 'Đã báo cáo seminar',

    666: 'Chưa đăng ký bảo vệ',
    667: 'Chờ giảng viên chấp nhận đăng ký bảo vệ',
    668: 'Chưa nộp quyển theo đăng ký bảo vệ',
    669: 'Chờ xuất trình đăng ký bảo vệ',
    670: 'Chờ quyết định bảo vệ',

    700: 'Đang bảo vệ đề tài',

    0: 'Đã bảo vệ không thành công',
    1: 'Đã xin thôi',
    2: 'Đã bảo vệ thành công',
    3: 'Đã quá hạn'
}

// Step name config
export const getStepName = (stepId, lecturerName) => {
    switch (stepId) {
        // Register
        case 100:
            return `Xin đăng ký đề tài với giảng viên ${lecturerName}`
        case 4000:
            return `Giảng viên ${lecturerName} từ chối hướng dẫn đề tài`
        case 4001:
            return `Giảng viên ${lecturerName} chấp nhận hướng dẫn đề tài`
        case 4002:
            return `Giảng viên ${lecturerName} chỉnh sửa đề tài`
        case 200:
            return `Xuất trình đề tài`
        case 210:
            return `Xác thực giảng viên đơn vị ngoài cho đề tài`
        case 212:
            return `Xác thực giảng viên đơn vị ngoài cho yêu cầu thay đổi đề tài`
        case 300:
            return `Ghi nhận phê duyệt đề tài`
        case 5000:
            return `Bộ môn thẩm định và từ chối`
        case 5001:
            return `Bộ môn thẩm định và chấp nhận`
        case 5002:
            return `Bộ môn thẩm định và yêu cầu chỉnh sửa`
        case 5003:
            return `Bộ môn phân giảng viên ${lecturerName} thẩm định đề tài`
        case 2100:
            return `Khoa cho phép chỉnh sửa lại yêu cầu đề tài`
        // Editing
        case 101:
            return `Xin điều chỉnh đề tài`
        case 4010:
            return `Giảng viên ${lecturerName} từ chối đề nghị chỉnh sửa`
        case 4011:
            return `Giảng viên ${lecturerName} chấp nhận đề nghị chỉnh sửa`
        case 2010:
            return `Khoa từ chối đề nghị chỉnh sửa`
        case 2011:
            return `Xuất trình đề nghị chỉnh sửa`
        case 211:
            return `Khoa mở đợt điều chỉnh`
        case 2012:
            return `Khoa đóng đợt điều chỉnh`
        case 301:
            return `Ghi nhận phê duyệt đề nghị chỉnh sửa`
        // Extending
        case 102:
            return `Xin gia hạn đề tài`
        case 4020:
            return `Giảng viên ${lecturerName} từ chối đề nghị xin gia hạn`
        case 4021:
            return `Giảng viên ${lecturerName} chấp nhận đề nghị xin gia hạn`
        case 2020:
            return `Khoa từ chối đề nghị gia hạn`
        case 2021:
            return `Xuất trình đề nghị gia hạn`
        case 2022:
            return `Khoa chỉnh sửa đề nghị gia hạn`
        case 302:
            return `Ghi nhận phê duyệt đề nghị gia hạn`
        // Cancel
        case 103:
            return `Xin thôi làm đề tài`
        case 4030:
            return `Giảng viên ${lecturerName} từ chối đề nghị xin thôi`
        case 4031:
            return `Giảng viên ${lecturerName} chấp nhận đề nghị xin thôi`
        case 2030:
            return `Khoa từ chối đề nghị xin thôi`
        case 2031:
            return `Xuất trình đề nghị xin thôi`
        case 303:
            return `Ghi nhận phê duyệt đề nghị xin thôi`
        // Pause
        case 104:
            return `Xin tạm hoãn làm đề tài`
        case 4040:
            return `Giảng viên ${lecturerName} từ chối đề nghị tạm hoãn`
        case 4041:
            return `Giảng viên ${lecturerName} chấp nhận đề nghị tạm hoãn`
        case 2040:
            return `Khoa từ chối đề nghị tạm hoãn`
        case 2041:
            return `Xuất trình đề nghị tạm hoãn`
        case 2042:
            return `Khoa chỉnh sửa đề nghị tạm hoãn`
        case 304:
            return `Ghi nhận phê duyệt đề nghị tạm hoãn`
        default:
            return `Thao tác chưa có trong danh sách`
        // Defense register
        case 105:
            return `Học viên đăng ký bảo vệ`
        case 4050:
            return `Giảng viên ${lecturerName} từ chối đăng ký bảo vệ của học viên`
        case 4051:
            return `Giảng viên ${lecturerName} chấp nhận đăng ký bảo vệ của học viên`
        case 2051:
            return `Khoa đánh dấu đề tài đã nộp quyển`
        case 2049:
            return `Khoa đánh dấu đề tài chưa nộp quyển`
        case 2050:
            return `Khoa từ chối đề nghị đăng ký bảo vệ`
        case 2052:
            return `Xuất trình đề nghị đăng ký bảo vệ`
        case 305:
            return `Ghi nhận phê duyệt đề nghị đăng ký bảo vệ`
        case 2053:
            return `Khoa mở đợt đăng ký bảo vệ`
        case 2054:
            return `Khoa đóng đợt đăng ký bảo vệ`
        case 2081:
            return `Khoa đánh dấu đề tài bảo vệ thành công`
        case 2080:
            return `Khoa đánh dấu đề tài bảo vệ không thành công`
        case 2082:
            return `Khoa đánh dấu đề tài quá hạn`
        // Seminar
        case 106:
            return `Học viên đăng ký seminar`
        case 2090:
            return `Khoa mở quyền đăng ký seminar`
        case 2091:
            return `Khoa đánh dấu đề tài đã seminar`
        case 4060:
            return `Giảng viên ${lecturerName} từ chối đăng ký seminar của học viên`
        case 4061:
            return `Giảng viên ${lecturerName} chấp nhận đăng ký seminar của học viên`
    }
}
