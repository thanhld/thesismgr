import React, { Component } from 'react'
import axios from 'axios'
import moment from 'moment'
import { Link } from 'react-router'
import { notify } from 'Components'
import { TOPIC_STATUS } from 'Config'
import { isNeedToConfirmTopic,
    getNextLecturerStep,
    topicWorking,
    isTopicEdit,
    isTopicPause,
    isTopicExtend,
    isTopicCancel,
    canCancelRequest,
    getNewSupervisorIds,
    confirmMessage,
    declineMessage } from 'Helper'
import { LECTURER_ACCEPT_TOPIC, LECTURER_DECLINE_TOPIC, OFFICER_LECTURER_ROLE } from 'Constants'

const getTopicChangeName = status => {
    if (status >= 890 && status <= 892) return 'Yêu cầu chỉnh sửa'
    if (status >= 893 && status <= 895) return 'Yêu cầu gia hạn'
    if (status >= 300 && status <= 302) return 'Yêu cầu tạm hoãn'
    if (status >= 200 && status <= 202) return 'Yêu cầu xin thôi'
}

class Topic extends Component {
    getLecturerById = id => {
        const lecturer = this.props.lecturers.find(l => l.id == id)
        if (lecturer) return lecturer
        else return false
    }
    getLecturerNameById = id => {
        if (id == 'del') return `Không có`
        const lecturer = this.getLecturerById(id)
        if (!lecturer) return false
        const { degreeId, fullname } = lecturer
        return `${this.getDegreeById(degreeId)} ${fullname}`
    }
    getOutLecturerNameById = id => {
        const lecturer = this.props.outOfficers.find(o => o.id == id)
        if (!lecturer) return false
        const { degreeId, fullname } = lecturer
        return `${this.getDegreeById(degreeId)} ${fullname} (Chưa xác minh)`
    }
    getDegreeById = id => {
        const degree = this.props.degrees.find(d => d.id == id)
        if (degree) return `${degree.name}.`
        else return false
    }
    checkPosition = (target, pos) => {
        const topic = target == 'main' ? this.props.topic : this.props.topic.topicChange
        if (!topic) return false
        const { mainSupervisorId, coSupervisorIds } = topic
        const { outOfficerIds } = this.props.topic
        let outIdsArr = ['', '']
        if (outOfficerIds) outIdsArr = outOfficerIds.split(',')
        if (pos == 0) {
            return mainSupervisorId || outIdsArr[0]
        } else if (pos == 1) {
            return coSupervisorIds || outIdsArr[1]
        }
        return false
    }
    getLecturerNameAtPosition = (target, pos) => {
        const topic = target == 'main' ? this.props.topic : this.props.topic.topicChange
        if (!topic) return false
        const { mainSupervisorId, coSupervisorIds } = topic
        const { outOfficerIds } = this.props.topic
        let outIdsArr = ['', '', '']
        if (outOfficerIds) outIdsArr = outOfficerIds.split(',')
        if (pos == 0) {
            if (mainSupervisorId) return this.getLecturerNameById(mainSupervisorId)
            if (outIdsArr[pos]) return this.getOutLecturerNameById(outIdsArr[pos])
        } else if (pos == 1) {
            if (coSupervisorIds) return this.getLecturerNameById(coSupervisorIds)
            if (outIdsArr[pos]) return this.getOutLecturerNameById(outIdsArr[pos])
        }
        return false
    }
    getTopicSupervisor = () => {
        const { topic } = this.props
        const { topicStatus } = topic
        if (topicStatus == 890) return topic.topicChange.requestedSupervisorId
        return topic.requestedSupervisorId
    }
    reloadData = callback => {
        this.props.reloadData(callback)
    }
    acceptTopic = () => {
        const val = confirm(confirmMessage(this.props.topic.topicStatus))
        if (val) {
            const { actions, topic, uid, lecturers } = this.props
            let data = {
                stepId: getNextLecturerStep(topic.topicStatus, true),
                topicId: topic.id,
                requestedSupervisorId: uid,
                newSupervisorIds: null,
                oldSupervisorIds: null
            }
            if (getNextLecturerStep(topic.topicStatus, true) == 4001) {
                const newSupervisorIds = getNewSupervisorIds('main', topic, lecturers)
                const oldSupervisorIds = null
                data['newSupervisorIds'] = newSupervisorIds
                data['oldSupervisorIds'] = oldSupervisorIds
            }
            if (getNextLecturerStep(topic.topicStatus, true) == 4011) {
                const newSupervisorIds = getNewSupervisorIds('sub', topic, lecturers)
                const oldSupervisorIds = getNewSupervisorIds('main', topic, lecturers)
                data['newSupervisorIds'] = newSupervisorIds
                data['oldSupervisorIds'] = oldSupervisorIds
            }
            actions.createActivity(LECTURER_ACCEPT_TOPIC, [data]).then(res => {
                const { data } = res.action.payload
                if (data.length > 0) {
                    notify.show(`Có lỗi xảy ra: ${data[0].error}`, 'danger')
                } else {
                    this.reloadData(() => {
                        notify.show(`Thầy/cô đã chấp nhận đề tài thành công`, 'primary')
                    })
                }
            }).catch(err => {
                notify.show(`Có lỗi xảy ra: ${err.response.data.message}`, 'danger')
            })
        }
    }
    declineTopic = () => {
        const { actions, topic, uid } = this.props
        const val = confirm(declineMessage(this.props.topic.topicStatus))
        if (val) {
            actions.createActivity(LECTURER_DECLINE_TOPIC, [{
                stepId: getNextLecturerStep(topic.topicStatus, false),
                topicId: topic.id,
                requestedSupervisorId: uid
            }]).then(res => {
                const { data } = res.action.payload
                if (data.length > 0) {
                    notify.show(`Có lỗi xảy ra: ${data[0].error}`, 'danger')
                } else {
                    this.reloadData(() => {
                        notify.show(`Thầy/cô đã từ chối đề tài thành công`, 'primary')
                    })
                }
            }).catch(err => {
                notify.show(`Có lỗi xảy ra: ${err.response.data.message}`, 'danger')
            })
        }
    }
    differLecturer = () => {
        const { topic } = this.props
        const { topicChange } = topic
        if (topic.mainSupervisorId != topicChange.mainSupervisorId) return true
        if (topic.coSupervisorIds != topicChange.coSupervisorIds) return true
        return false
    }
    shouldShowExpertise = () => {
        const { uid, topic } = this.props
        const { reviews } = topic
        if (!reviews) return false
        let shouldShow = false

        reviews.forEach(review => {
            if (review.officerId == uid && review.reviewStatus == 0) shouldShow = true
        })

        return shouldShow
    }
    render() {
        const { uid, topic, alreadyDetail, expertise } = this.props
        const { id, learnerId, topicStatus, topicType, isEnglish, vietnameseTopicTitle, englishTopicTitle, learner, description, tags, startDate, deadlineDate, topicChange, registerUrl } = topic
        if (!id) return false
        return <div>
            <div class="render-topic">
                <div class="row">
                    <div class="col-sm-12 col-md-8 topic-info-1">
                        { isEnglish == 1 ? `Tên đề tài: ${englishTopicTitle || 'Chưa có'}` : `Tên đề tài: ${vietnameseTopicTitle || 'Chưa có'}` }
                        { uid == topic.requestedSupervisorId && (topicStatus == 101 || topicStatus == 667) && !alreadyDetail && <i class="fa fa-exclamation-circle fa-lg text-danger margin-left" aria-hidden="true"></i> }
                    </div>
                    <div class="col-sm-11 col-md-4">
                        <div class="row">
                            <div class="col-xs-12">
                                { uid == this.getTopicSupervisor() && !alreadyDetail && isNeedToConfirmTopic(topicStatus) && <div class="pull-right">
                                    { topicStatus == 101 && <button class="btn btn-sm btn-margin btn-primary" data-toggle="modal" data-target="#lecturerEditTopic" onClick={e => this.props.setEditTopic(id)}>Chỉnh sửa</button> }
                                    <button class="btn btn-sm btn-margin btn-primary" onClick={this.acceptTopic}>Chấp nhận</button>
                                    <button class="btn btn-sm btn-margin btn-primary" onClick={this.declineTopic}>Từ chối</button>
                                </div> }
                                { uid == learnerId && <div class="pull-right">
                                    <div class="btn-group">
                                        <button type="button" class="btn btn-sm btn-primary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                            Thao tác <span class="caret"></span>
                                        </button>
                                        <ul class="dropdown-menu dropdown-menu-right">
                                            <li class={ topicStatus != 889 && "disabled"}><a class="clickable" onClick={this.props.requestChange}>Xin điều chỉnh</a></li>
                                            { topicType != 1 && <li class={ topicWorking(topicStatus) && "disabled"}><a class="clickable" onClick={e => this.props.changeRequestType('extend')}>Xin gia hạn</a></li> }
                                            { topicType != 1 && <li class={ topicWorking(topicStatus) && "disabled"}><a class="clickable" onClick={e => this.props.changeRequestType('pause')}>Xin tạm hoãn</a></li> }
                                            {/* <li class={ topicWorking(topicStatus) && "disabled"}><a class="clickable" onClick={e => !topicWorking(topicStatus) && this.props.changeRequestType('extend')}>Xin gia hạn</a></li>
                                            <li class={ topicWorking(topicStatus) && "disabled"}><a class="clickable" onClick={e => !topicWorking(topicStatus) && this.props.changeRequestType('pause')}>Xin tạm hoãn</a></li> */}
                                            <li class={ topicWorking(topicStatus) && "disabled"}><a class="clickable" onClick={e => !topicWorking(topicStatus) && this.props.changeRequestType('cancel')}>Xin thôi</a></li>
                                            { topicType != 1 && <li class={ topicStatus != 897 && "disabled"}><a class="clickable" onClick={e => topicStatus == 897 && this.props.changeRequestType('seminar')}>Xin đăng ký báo cáo tiến độ</a></li> }
                                            <li class={ topicStatus != 666 && "disabled"}><a class="clickable" onClick={e => topicStatus == 666 && this.props.changeRequestType('defense')}>Xin bảo vệ</a></li>
                                            { canCancelRequest(topicStatus) && <li><a class="clickable" onClick={this.props.
                                                cancelRequest}>Hủy thay đổi</a>
                                            </li> }
                                        </ul>
                                    </div>
                                </div> }
                                { uid != learnerId && !alreadyDetail && <div class="pull-right">
                                    <Link to={`/topic/${id}`} class="btn btn-sm btn-margin btn-primary">Chi tiết</Link>
                                </div> }
                                { expertise && this.shouldShowExpertise() && <button class="btn btn-sm btn-margin btn-primary pull-right" data-toggle="modal" data-target="#reviewTopic" onClick={e => this.props.changeReviewTarget(topic)}>Phản biện</button> }
                            </div>
                        </div>
                    </div>
                </div>
                { isEnglish == 1 && <div class="topic-sub-title">{vietnameseTopicTitle || 'Chưa có tên Vi'}</div> }
                <div class="topic-content">
                    <div class="row">
                        <div class="topic-info-2 col-xs-6">
                            <i class="fa fa-genderless fa-fw" aria-hidden="true"></i>
                            Học viên: {learner.fullname}
                        </div>
                        <div class="topic-info-2 col-xs-6">
                            <i class="fa fa-genderless fa-fw" aria-hidden="true"></i>
                            Mã học viên: {learner.learnerCode}
                        </div>
                    </div>
                    { this.checkPosition('main', 0) && <div class="topic-info-2">
                        <i class="fa fa-genderless fa-fw" aria-hidden="true"></i>
                        Giảng viên hướng dẫn chính: <span>{this.getLecturerNameAtPosition('main', 0)}</span>
                    </div> }
                    <div class="row">
                        { this.checkPosition('main', 1) && <div class="topic-info-2 col-md-6 col-sm-12">
                            <i class="fa fa-genderless fa-fw" aria-hidden="true"></i>
                            Giảng viên đồng hướng dẫn: <span>{this.getLecturerNameAtPosition('main', 1)}</span>
                        </div> }
                    </div>
                    <div class="topic-info-2">
                        <i class="fa fa-genderless fa-fw" aria-hidden="true"></i>
                        Mô tả: <span class="topic-description wrap-text">
                            {description || 'Chưa có nội dung'}
                        </span>
                    </div>
                    <div class="topic-info-2">
                        <i class="fa fa-genderless fa-fw" aria-hidden="true"></i>
                        Tags: {tags || 'Chưa có tags'}
                    </div>
                    { topicType == 2 && <div class="topic-info-2">
                        <i class="fa fa-genderless fa-fw" aria-hidden="true"></i>
                        Đề cương: <a href={registerUrl} download>
                            Tải xuống
                        </a>
                    </div> }
                    <div class="row">
                        <div class="col-md-6 col-xs-12">
                            <div class="topic-info-2">
                                <i class="fa fa-genderless fa-fw" aria-hidden="true"></i>
                                Ngày bắt đầu: {(startDate && moment(startDate).format('L')) || 'Chưa có'}
                            </div>
                        </div>
                        <div class="col-md-6 col-xs-12">
                            <div class="topic-info-2">
                                <i class="fa fa-genderless fa-fw" aria-hidden="true"></i>
                                Ngày bảo vệ: {(deadlineDate && moment(deadlineDate).format('L')) || 'Chưa có'}
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-12 col-md-9">
                            <div class="topic-info-2">
                                <i class="fa fa-genderless fa-fw" aria-hidden="true"></i>
                                Trạng thái: {TOPIC_STATUS[topicStatus]}
                            </div>
                        </div>
                    </div>
                </div>
                { topicChange && <div>
                    <hr />
                    <div class="topic-sub-title-2">{getTopicChangeName(topicStatus)} {uid == this.getTopicSupervisor() && isNeedToConfirmTopic(topicStatus) && <i class="fa fa-exclamation-circle fa-lg text-danger" aria-hidden="true"></i>}</div>
                    { isTopicEdit(topicStatus) && <div>
                        { topicChange.vietnameseTopicTitle && <div class="topic-info-2">
                            <i class="fa fa-genderless fa-fw" aria-hidden="true"></i>
                            Tên đề tài (Vi): {topicChange.vietnameseTopicTitle}
                        </div> }
                        { topicChange.englishTopicTitle && <div class="topic-info-2">
                            <i class="fa fa-genderless fa-fw" aria-hidden="true"></i>
                            Tên đề tài (En): {topicChange.englishTopicTitle}
                        </div> }
                        { this.differLecturer() && <div>
                            { this.checkPosition('sub', 0) && <div class="topic-info-2">
                                <i class="fa fa-genderless fa-fw" aria-hidden="true"></i>
                                Giảng viên hướng dẫn chính: {this.getLecturerNameAtPosition('sub', 0)}
                            </div> }
                            <div>
                                { this.checkPosition('sub', 1) && <div class="topic-info-2">
                                    <i class="fa fa-genderless fa-fw" aria-hidden="true"></i>
                                    Giảng viên đồng hướng dẫn: <span>{this.getLecturerNameAtPosition('sub', 1)}</span>
                                </div> }
                            </div>
                        </div> }
                        { topicChange.description && <div class="topic-info-2">
                            <i class="fa fa-genderless fa-fw" aria-hidden="true"></i>
                            Mô tả: <span class="topic-description wrap-text">{topicChange.description || 'Chưa có mô tả'}</span>
                        </div> }
                        { topicChange.tags && <div class="topic-info-2">
                            <i class="fa fa-genderless fa-fw" aria-hidden="true"></i>
                            Tags: <span>{topicChange.tags || 'Chưa có tags'}</span>
                        </div> }
                    </div> }
                    { isTopicExtend(topicStatus) && <div>
                        { topicChange.delayDuration && <div class="topic-info-2">
                            <i class="fa fa-genderless fa-fw" aria-hidden="true"></i>
                            Thời gian gia hạn: <span>{topicChange.delayDuration} tháng</span>
                        </div> }
                    </div> }
                    { isTopicPause(topicStatus) && <div>
                        { topicChange.startPauseDuration && <div class="topic-info-2">
                            <i class="fa fa-genderless fa-fw" aria-hidden="true"></i>
                            Ngày bắt đầu tạm hoãn: <span>{moment(topicChange.pauseDuration).format('L')}</span>
                        </div> }
                        { topicChange.pauseDuration && <div class="topic-info-2">
                            <i class="fa fa-genderless fa-fw" aria-hidden="true"></i>
                            Thời gian tạm hoãn: <span>{topicChange.pauseDuration} tháng</span>
                        </div> }
                    </div> }
                    { isTopicCancel(topicStatus) && <div>
                        { topicChange.cancelReason && <div class="topic-info-2">
                            <i class="fa fa-genderless fa-fw" aria-hidden="true"></i>
                            Lí do xin thôi: <span>{topicChange.cancelReason || 'Không có'}</span>
                        </div> }
                    </div> }
                </div> }
            </div>
            <hr />
        </div>
    }
}

export default Topic
