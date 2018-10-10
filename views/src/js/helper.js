import {
    SUPERUSER_ROLE,
    ADMIN_ROLE,
    LEARNER_ROLE,
    OFFICER_LECTURER_ROLE,
    OFFICER_ADMIN_ROLE,
    OUT_OFFICER_ROLE,
    DEPARTMENT_ROLE
} from 'Constants'
import { routeName } from 'Config'

export const isSuperuser = (user) => {
    return user && (user.role == SUPERUSER_ROLE)
}

export const isAdmin = (user) => {
    return user && (user.role == ADMIN_ROLE)
}

export const isOfficerAdmin = (user) => {
    const officerAdminArr = [ADMIN_ROLE, OFFICER_ADMIN_ROLE]
    return user && (officerAdminArr.includes(user.role)) // arr.includes(i): true if i in arr, false otherwise
}

export const isDepartment = (user) => {
    return user && (user.role == DEPARTMENT_ROLE)
}

export const isLecturer = (user) => {
    const lecturerArr = [OFFICER_LECTURER_ROLE, DEPARTMENT_ROLE]
    return user && (lecturerArr.includes(user.role)) // arr.includes(i): true if i in arr, false otherwise
}

export const isLearner = (user) => {
    return user && (user.role == LEARNER_ROLE)
}

export const isNeedToConfirmTopic = status => {
    const needConfirmArr = [101, 890, 893, 200, 300, 667, 898]
    return needConfirmArr.includes(status)
}

export const getNextLecturerStep = (status, bool) => {
    const needConfirmArr = [101, 890, 893, 200, 300, 667, 898]
    const confirmStep = [4000, 4010, 4020, 4030, 4040, 4050, 4060]
    let resultIndex = needConfirmArr.findIndex(e => e == status)
    let resultValue = confirmStep[resultIndex]
    if (bool) resultValue++
    return resultValue
}

export const getNextAdminDeclineStep = status => {
    if (status == 891 || status == 892) return 2010
    if (status == 894 || status == 895) return 2020
    if (status == 201 || status == 202) return 2030
    if (status == 301 || status == 302) return 2040
    if (status >= 668 && status <= 670) return 2050
    return 'Error'
}

export const canCancelRequest = status => {
    if (status >= 890 && status <= 891) return true
    if (status >= 893 && status <= 894) return true
    if (status >= 200 && status <= 201) return true
    if (status >= 300 && status <= 301) return true
    return false
}

export const getNewSupervisorIds = (target, thisTopic, lecturers) => {
    let result = ['', '']
    const topic = target == 'main' ? thisTopic : thisTopic.topicChange
    const { outOfficerIds } = thisTopic
    let { mainSupervisorId, coSupervisorIds } = topic
    if (!mainSupervisorId) mainSupervisorId = thisTopic.mainSupervisorId
    if (!coSupervisorIds) coSupervisorIds = thisTopic.coSupervisorIds
    // Check if mainSupervisorId role 3
    let cnt = 0
    const mainSupervisor = lecturers.find(l => l.id == mainSupervisorId)
    if (mainSupervisor && mainSupervisor.role == OFFICER_LECTURER_ROLE) {
        if (target == 'main' || !outOfficerIds || !outOfficerIds.split(',')[cnt])
            result[cnt] = mainSupervisorId
    }
    cnt++
    if (coSupervisorIds  != 'del') {
        const coSupervisor = lecturers.find(l => l.id == coSupervisorIds)
        if (coSupervisor && coSupervisor.role == OFFICER_LECTURER_ROLE) {
            if (target == 'main' || !outOfficerIds || !outOfficerIds.split(',')[cnt]) {
                result[cnt] = coSupervisorIds
            }
        }
    }
    cnt++
    return result.join(',')
}

export const topicWorking = status => {
    return status != 888 && status != 889
}

export const isTopicEdit = status => {
    return status >= 890 && status <= 892
}

export const isTopicExtend = status => {
    return status >= 893 && status <= 895
}

export const isTopicPause = status => {
    return status >= 300 && status <= 302
}

export const isTopicCancel = status => {
    return status >= 200 && status <= 202
}

export const stepIdToDocumentType = stepId => {
    if ([200, 2011, 2021, 2031, 2041, 2052].includes(stepId)) return `Tờ trình`
    if ([300, 301, 302, 303, 304, 305].includes(stepId)) return `Quyết định`
    return ``
}

export const reviewStatusToText = status => {
    switch (status) {
        case 0:
            return 'chưa nhận xét'
        case 1:
            return 'chấp nhận đề cương'
        case 3:
            return 'không chấp nhận đề cương'
        case 2:
            return 'yêu cầu đề cương chỉnh sửa'
        case 4:
            return 'từ chối phản biện'
        default:
            return 'Không tìm thấy trạng thái'
    }
}

export const topicTypeToName = type => {
    if (type == routeName['ADMIN_TOPIC_STUDENT']) return 'khóa luận tốt nghiệp'
    if (type == routeName['ADMIN_TOPIC_GRADUATED']) return 'luận văn cao học'
    if (type == routeName['ADMIN_TOPIC_RESEARCHER']) return 'luận án tiến sĩ'
    return ''
}

export const topicTypeToTypeId = type => {
    if (type == routeName['ADMIN_TOPIC_STUDENT']) return 1
    if (type == routeName['ADMIN_TOPIC_GRADUATED']) return 2
    if (type == routeName['ADMIN_TOPIC_RESEARCHER']) return 3
    return ''
}

export const topicTypeToLearner = type => {
    if (type == routeName['ADMIN_TOPIC_STUDENT']) return 'sinh viên'
    if (type == routeName['ADMIN_TOPIC_GRADUATED']) return 'học viên cao học'
    if (type == routeName['ADMIN_TOPIC_RESEARCHER']) return 'nghiên cứu sinh'
    return ''
}

export const departmentTypeToName = (type) => {
    switch (type) {
        case "4":
            return 'Văn phòng Khoa'
        case "1":
            return 'Bộ môn'
        case "2":
            return 'Phòng thí nghiệm'
        case "3":
            return 'Đơn vị ngoài'
        default:
            return ''
    }
}

export const confirmMessage = status => {
    switch (status) {
        case 101:
            return 'Thầy/cô chấp nhận hướng dẫn đề tài này?'
        case 890:
            return 'Thầy/cô chấp nhận yêu cầu chỉnh sửa này?'
        case 893:
            return 'Thầy/cô chấp nhận yêu cầu gia hạn này?'
        case 200:
            return 'Thầy/cô chấp nhận yêu cầu xin thôi này?'
        case 300:
            return 'Thầy/cô chấp nhận yêu cầu xin tạm hoãn này?'
        case 667:
            return 'Thầy/cô cho phép đề tài này được quyền bảo vệ?'
        default:
            return 'Thầy/cô đồng ý yêu cầu này?'
    }
}

export const getDepartmentMessage = stepId => {
    switch (stepId) {
        case 5000:
            return 'Thầy/cô từ chối phê duyệt thẩm định đề tài này?\nSau khi từ chối, đề tài sẽ phải đăng ký lại từ đầu.'
        case 5001:
            return 'Thầy/cô đồng ý phê duyệt thẩm định đề tài này?\nSau khi đồng ý, đề tài sẽ được Văn phòng khoa tổng hợp để xuất trình lên nhà trường.'
        case 5002:
            return 'Thầy/cô yêu cầu đề tài hoàn thiện thêm đề cương?\nSau khi đồng ý, đề tài sẽ trở lại trạng thái đăng ký, học viên sẽ cập nhật lại đề cương.'
        default:
            return 'Thầy/cô đồng ý yêu cầu này?'
    }
}

export const declineMessage = status => {
    switch (status) {
        case 101:
            return 'Thầy/cô từ chối hướng dẫn đề tài này?'
        case 890:
            return 'Thầy/cô từ chối yêu cầu chỉnh sửa này?'
        case 893:
            return 'Thầy/cô từ chối yêu cầu gia hạn này?'
        case 200:
            return 'Thầy/cô từ chối yêu cầu xin thôi này?'
        case 300:
            return 'Thầy/cô từ chối yêu cầu xin tạm hoãn này?'
        case 667:
            return 'Thầy/cô từ chối đề tài này được quyền bảo vệ?'
        default:
            return 'Thầy cô đồng ý yêu cầu này?'
    }
}

export const sortDepartments = (a, b) => {
    if (a.type == "4" && b.type != "4") return -1
    else if (a.type != "4" && b.type == "4") return 1
    else if (a.type < b.type) return -1
    else if (a.type > b.type) return 1
    return a.name.localeCompare(b.name)
}

export const sortOfficers = (a, b) => {
    // if (a.role < b.role) return -1
    // if (a.role > b.role) return 1
    let aName = a.fullname.split(" ").slice(-1).pop()
    let bName = b.fullname.split(" ").slice(-1).pop()
    return aName.localeCompare(bName)
}

export const sortLearners = (a, b) => {
    if (a.trainingCourseCode < b.trainingCourseCode) return -1
    if (a.trainingCourseCode > b.trainingCourseCode) return 1
    let aName = a.fullname.split(" ").slice(-1).pop()
    let bName = b.fullname.split(" ").slice(-1).pop()
    return aName.localeCompare(bName)
}

export const sortTopics = (a, b) => {
    let aName = a.learner.fullname.split(" ").slice(-1).pop()
    let bName = b.learner.fullname.split(" ").slice(-1).pop()
    return aName.localeCompare(bName)
}

export const array = (n, func) => {
    return Array(n).fill().map(x => func())
}

export const arrayRange = (start, end) => {
    start = parseInt(start)
    end = parseInt(end)
    return Array(end - start + 1).fill().map((_, idx) => start + idx)
}

//Convert datetime MySQL to format yyyy/mm/dd
export const formatDate = (datetime) => {
    return new Date(datetime).toISOString().slice(0,10);
}

export const formatMoment = datetime => {
    return datetime.format('YYYY-MM-DD HH:mm:ss')
}

export const getCurrentYear = () => {
    return new Date().getFullYear()
}

import moment from 'moment'
export const dateMoment = datetime => {
    return moment(datetime).format('HH:mm, DD/MM/YYYY')
}
