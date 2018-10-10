<?php
/**
 * Created by PhpStorm.
 * User: Kira
 * Date: 3/20/2017
 * Time: 08:53 PM
 */

namespace core\utility;

class Constant
{
    const updated = "Cập nhật thành công";
    const notUpdated = "Không thay đổi";
    const success = "Thành công";
    const failed = "Không thành công";
    const notChangeTopicStatus = ' không thể thay đổi trạng thái của đề tài này hiện tại';

    const emptyList = "Danh sách dữ liệu trống";
    const invalidText = " không hợp lệ";
    const notFoundText = "Không tìm thấy ";
    const notExistedText = " không tồn tại";
    const isExistedText = " đã tồn tại";
    const missingText = "Hiện chưa có tiến trình xử lý ";
    const notAuthenticationText = "Bạn chưa đăng nhập";
    const notPermissionText = "Bạn không được cấp quyền thực hiện tác vụ này";
    const errorText = "Có lỗi xảy ra";
    const connectionText = "Lỗi kết nối với cơ sở dữ liệu: ";
    const notDefinedText = " chưa được định nghĩa.";
    const isRequiredText = " là bắt buộc";
    const notEmptyText = " không được để trống";
    const unknownText = " không xác định";
    const cannotDelete = "Không thể xóa ";
    const outOfMaxQuota = "Vượt quá ";

    const columnNames = array(
        //common
        'id' => 'mã bản ghi',
        'type' => 'loại',
        'name' => 'tên',
        'address' => 'địa chỉ',
        'phone' => 'số điện thoại',
        'website' => 'trang thông tin',

        //accounts
        'uid' => 'uid',
        'username' => 'tên tài khoản',
        'password' => 'mật khẩu',
        'vnuMail' => 'vnu mail',
        'role' => 'vai trò người dùng',
        'securityToken' => 'mã bảo mật',

        //activities
        'documentId' => 'id tài liệu',
        'accountId' => 'id tài khoản',
        'stepId' => 'id bước',
        'requestedSupervisorId' => 'id giảng viên được yêu cầu phê duyệt',

        //activities_topics
        'topicId' => 'id đề tài',
        'activitiesId' => 'id hoạt động',

        //announcements
        'facultyId' => 'id Khoa',
        'title' => 'tiêu đề',
        'tags' => 'các thẻ gắn',
        'content' => 'nội dung',
        'showDate' => 'ngày hiện thông báo',
        'hideDate' => 'ngày ẩn thông báo',

        //areas_officers
        'knowledgeAreaId' => 'id lĩnh vực quan tâm',
        'officerId' => 'id cán bộ',

        //attachments
        'announcementId' => 'id thông báo',
        'url' => 'đường dẫn',

        //departments

        //dict_degrees
        //dict_knowledge_areas
        'parentId' => 'id lĩnh vực cha',

        //dict_steps
        'stepCode' => 'mã bước',
        'stepName' => 'tên bước',
        'nextStepId' => 'id bước tiếp theo',
        'nextTopicStatus' => 'trạng thái chuyển tiếp của đề tài',

        //dict_training_areas
        'areaCode' => 'mã ngành đào tạo',

        //dict_training_courses
        'trainingProgramId' => 'id chương trình đào tạo',
        'courseCode' => 'mã khóa đào tạo',
        'courseName' => 'tên khóa đào tạo',
        'admissionYear' => 'năm bắt đầu',
        'isCompleted' => 'trạng thái đã hoàn thành',

        //dict_training_levels
        'levelType' => 'loại bậc đào tạo',

        //dict_training_programs
        'departmentId' => 'id đơn vị',
        'trainingAreasId' => 'id ngành đào tạo',
        'trainingLevelsId' => 'id bậc đào tạo',
        'trainingTypesId' => 'id hình thức đào tạo',
        'programCode' => 'mã chương trình đào tạo',
        'vietnameseThesisTitle' => 'tiêu đề đề tài (tiếng Việt)',
        'englishThesisTitle' => 'tiêu đề đề tái (tiếng Anh)',
        'startTime' => 'thời gian bắt đầu chương trình đào tạo',
        'trainingDuration' => 'thời gian đào tạo',
        'isInUse' => 'trạng thái đang có hiêu lực',

        //dict_training_types

        //documents
        'documentCode' => 'mã văn bản',
        'createdDate' => 'ngày kí văn bản',

        //faculties
        'shortName' => 'tên viết tắt',

        //learners
        'trainingCourseId' => 'id khóa đào tạo',
        'fullname' => 'họ và tên',
        'learnerCode' => 'mã học viên',
        'learnerType' => 'hình thức học viên',
        'otherEmail' => 'địa chỉ mail khác',
        'avatarUrl' => 'đường dẫn ảnh cá nhân',
        'gpa' => 'điểm tổng kết',
        'description' => 'thông tin chi tiết',

        //officers
        'officerCode' => 'mã cán bộ',
        'degreeId' => 'id học hàm, học vị',
        'numberOfStudent' => 'số sinh viên đang hướng dẫn',
        'numberOfGraduated' => 'số học viên cao học đang hướng dẫn',
        'numberOfResearcher' => 'số nghiên cứu sinh đang hướng dẫn',

        'newSupervisorIds' => "danh sách giảng viên tăng số lượng hướng dẫn ",
        'oldSupervisorIds' => "danh sách giảng viên giảm số lượng hướng dẫn ",

        //out_officers
        'departmentName' => 'tên đơn vị công tác',

        //topics
        'learnerId' => 'id học viên',
        'topicType' => 'loại để tài',
        'vietnameseTopicTitle' => 'tên đề tài (tiếng Việt)',
        'englishTopicTitle' => 'tên đề tài (tiếng Anh)',
        'isEnglish' => 'báo cáo đề tài bằng tiếng Anh?',
        'referenceUrl' => 'đường dẫn tài liệu tham khảo',
        'mainSupervisorId' => 'id giảng viên hướng dẫn chính',
        'coSupervisorIds' => 'id các giảng viên hướng dẫn phụ',
        'outOfficerIds' => 'id các giảng viên ngoài',
        'outOfficerId' => 'id giảng viên ngoài',
        'startDate' => 'ngày bắt đầu thực hiện',
        'defaultDeadlineDate' => 'ngày kết thúc đề tài (mặc định)',
        'deadlineDate' => 'ngày kết thúc đề tài (thực tế)',
        'registerUrl' => 'đường dẫn văn bản đăng ký luận văn',
        'expertiseOfficerIds' => 'id các giảng viên phản biện',

        //topics_changes
        'startPauseDate' => 'ngày bắt đầu hoãn đề tài',
        'pauseDuration' => 'thời gian tạm hoãn đề tài',
        'delayDuration' => 'thời gian gia hạn đề tài',
        'cancelReason' => 'lí do xin thôi đề tài',

        //quotas
        'version' => 'phiên bản',
        'isActive' => 'trạng thái hoạt động',
        'maxStudent' => 'định mức sinh viên',
        'maxGraduated' => 'định mức học viên cao học',
        'maxResearcher' => 'định mức nghiên cứu sinh',
        'mainFactorStudent' => 'hệ số sinh viên của giảng viên chính',
        'mainFactorGraduated' => 'hệ số học viên cao học của giảng viên chính',
        'mainFactorResearcher' => 'hệ số nghiên cứu sinh của giảng viên chính',
        'coFactorStudent' => 'hệ số sinh viên của giảng viên phụ',
        'coFactorGraduated' => 'hệ số học viên cao học của giảng viên phụ',
        'coFactorResearcher' => 'hệ số nghiên cứu sinh của giảng viên phụ',

        //reviews
        'content' => 'nội dung nhận xét',
        'reviewStatus' => 'trạng thái nhận xét'
    );

    const objectNames = array(
        'faculty' => 'Khoa',
        'department' => 'đơn vị công tác',
        'facultyOfficer' => 'văn phòng Khoa',
        'officer' => 'cán bộ',
        'lecturer' => 'giảng viên',
        'outOfficer' => 'giảng viên ngoài',
        'learner' => 'học viên',
        'admin' => 'chuyên viên',

        'activity' => 'hoạt động',
        'account' => 'tài khoản',
        'announcement' => 'thông báo',
        'attachment' => 'tệp đính kém',
        'degree' => 'học hàm, học vị',
        'document' => 'tài liệu',
        'knowledgeArea' => 'lĩnh vực nghiên cứu',
        'quotas' => 'định mức hướng dẫn',
        'step' => 'bước thực hiện',
        'topic' => 'đề tài',
        'topicChange' => 'đơn thay đổi đề tài',
        'topicHistory' => 'lịch sử thay đổi đề tài',
        'trainingArea' => 'ngành đào tạo',
        'trainingCourse' => 'khóa đào tạo',
        'trainingLevel' => 'bậc đào tạo',
        'trainingProgram' => 'chương trình đào tạo',
        'trainingType' => 'hình thức đào tạo',

        'review' => 'nhận xét của giảng viên phản biện'
    );
}