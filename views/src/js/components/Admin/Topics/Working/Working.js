import React, { Component } from 'react'
import { Modal, notify } from 'Components'
import { array } from 'Helper'
import ManageTopicChange from '../ManageTopicChange'
import { routeName, SEMINAR_DURATION } from 'Config'
import { ADMIN_OPEN_TOPIC_SEMINAR } from 'Constants'
import { mailerActions } from 'Actions';

class AdminTopicsWorking extends Component {
    constructor(props) {
        super(props)
        this.state = {
            error: false,
            message: '',
            title: '',
            cur_topics: [],
            checked: [],
            type: ''
        }
    }
    filterToOpen = () => {
        const { topics } = this.props
        const tmp = topics.filter(t => t.topicStatus == 888)
        this.setState({
            error: false,
            title: 'Mở chỉnh sửa đề tài',
            cur_topics: tmp,
            checked: array(tmp.length, () => true),
            type: 'open'
        })
    }
    filterToOpenRegister = () => {
        const { topics } = this.props
        const tmp = topics.filter(t => t.topicStatus == 888)
        this.setState({
            error: false,
            title: 'Mở quyền đăng ký bảo vệ',
            cur_topics: tmp,
            checked: array(tmp.length, () => true),
            type: 'open-defense'
        })
    }
    filterOpenSeminar = () => {
        const { topics } = this.props
        const tmp = topics.filter(t => t.topicStatus == 888 && t.processDuration >= SEMINAR_DURATION)
        this.setState({
            error: false,
            title: 'Mở quyền đăng ký báo cáo tiến độ',
            cur_topics: tmp,
            checked: array(tmp.length, () => true),
            type: 'open-seminar'
        })
    }
    showAllSeminar = () => {
        const { topics } = this.props
        const tmp = topics.filter(t => t.topicStatus == 888)
        const checked = tmp.map(t => t.processDuration >= SEMINAR_DURATION)
        this.setState({
            error: false,
            title: 'Mở quyền đăng ký báo cáo tiến độ',
            cur_topics: tmp,
            checked,
            type: 'open-seminar'
        })
    }
    isTopicLessDuration = () => {
        const { topics } = this.props
        const tmp = topics.filter(t => t.topicStatus == 888 && t.processDuration >= SEMINAR_DURATION)
        return tmp.length < topics.length
    }
    filterToClose = () => {
        const { topics } = this.props
        const tmp = topics.filter(t => t.topicStatus == 889)
        this.setState({
            error: false,
            title: 'Đóng chỉnh sửa đề tài',
            cur_topics: tmp,
            checked: array(tmp.length, () => true),
            type: 'close'
        })
    }
    handleToggleCheckAll = e => {
        const length = this.state.checked.length
        this.setState({
            checked: array(length, () => e.target.checked)
        })
    }
    handleCheck = index => e => {
        let newCheck = [...this.state.checked]
        newCheck[index] = !newCheck[index]
        this.setState({
            checked: newCheck
        })
    }
    handleSubmit = e => {
        e.preventDefault()
        const { checked, cur_topics, type } = this.state
        const { actions, reloadData } = this.props
        let topicData = []
        const stepId = type == 'open' ? 211
            : type == 'close' ? 2012
            : type == 'open-defense' ? 2053
            : type == 'open-seminar' ? 2090
            : 0
        // Fail case
        if (stepId == 0) {
            notify.show('Bước thực hiện không xác định. Vui lòng kiểm tra lại.', 'danger')
            $('#topic-change').modal('hide')
            return false
        }
        checked.forEach((c, index) => {
            if (!c) return
            topicData.push({
                stepId,
                topicId: cur_topics[index].id
            })
        })
        if ( type != 'open-defense' && type != 'open-seminar') {
            actions.requestChange(topicData).then(res => {
                const { data } = res.action.payload
                if (data.length > 0) {
                    const mess = data.map(d => {
                        const t = cur_topics.find(t => d.topicId == t.id)
                        const e = d.error
                        return `${t && t.learner.learnerCode}: ${e}`
                    })
                    this.setState({
                        error: true,
                        message: mess.join('; ')
                    })
                } else {
                    this.props.reloadData(() => {
                        $('#topic-change').modal('hide')
                        notify.show('Thầy/cô cập nhật đề tài thành công', 'primary')
                    })
                }
                if(type == 'open') mailerActions.changeTopicMail();
            }).catch(err => {
                notify.show(`Có lỗi xảy ra: ${err.response.data.message}`, 'danger')
            })
        } else if (type == 'open-defense') {
            actions.requestProtect(topicData).then(res => {
                const { data } = res.action.payload
                if (data.length > 0) {
                    const mess = data.map(d => {
                        const t = cur_topics.find(t => d.topicId == t.id)
                        const e = d.error
                        return `${t && t.learner.learnerCode}: ${e}`
                    })
                    this.setState({
                        error: true,
                        message: mess.join('; ')
                    })
                } else {
                    this.props.reloadData(() => {
                        $('#topic-change').modal('hide')
                        notify.show('Thầy/cô cập nhật đề tài thành công', 'primary')
                    })
                }
                mailerActions.protectTopicMail();
            }).catch(err => {
                notify.show(`Có lỗi xảy ra: ${err.response.data.message}`, 'danger')
            })
        } else if (type == 'open-seminar') {
            actions.createActivity(ADMIN_OPEN_TOPIC_SEMINAR, topicData).then(res => {
                const { data } = res.action.payload
                if (data.length > 0) {
                    const mess = data.map(d => {
                        const t = cur_topics.find(t => d.topicId == t.id)
                        const e = d.error
                        return `${t && t.learner.learnerCode}: ${e}`
                    })
                    this.setState({
                        error: true,
                        message: mess.join('; ')
                    })
                } else {
                    this.props.reloadData(() => {
                        $('#topic-change').modal('hide')
                        notify.show('Thầy/cô cập nhật đề tài thành công', 'primary')
                    })
                }
                mailerActions.seminarTopicMail();
            }).catch(err => {
                notify.show(`Có lỗi xảy ra: ${err.response.data.message}`, 'danger')
            })
        }
    }
    handleCancel = () => {
        this.setState({
            error: false
        })
    }
    render() {
        const { checked, cur_topics, title, error, message, stepId, type } = this.state
        const { actions, topics, reloadData, degrees, lecturers } = this.props
        const numberTopicsNotAbleSeminar = topics.length - cur_topics.length

        return <div class="topic-admin-header">
            <button class="btn btn-sm btn-margin btn-success" data-toggle="modal" data-target="#topic-change" onClick={this.filterToOpen}>Mở quyền điều chỉnh đề tài</button>
            <button class="btn btn-sm btn-margin btn-success margin-right" data-toggle="modal" data-target="#topic-change" onClick={this.filterToClose}>Đóng quyền điều chỉnh đề tài</button>
            { this.props.type != routeName['ADMIN_TOPIC_STUDENT'] && <button class="btn btn-sm btn-margin btn-success margin-right" data-toggle="modal" data-target="#topic-change" onClick={this.filterOpenSeminar}>Mở quyền đăng ký kiểm tra tiến độ</button> }
            { this.props.type == routeName['ADMIN_TOPIC_STUDENT'] && <button class="btn btn-sm btn-margin btn-success" data-toggle="modal" data-target="#topic-change" onClick={this.filterToOpenRegister}>Mở quyền đăng ký bảo vệ</button> }
            <Modal
                modalId="topic-change"
                title={title}
                onSubmit={this.handleSubmit}
                onCancel={this.handleCancel}>
                {/* Toggle show seminar block */}
                { type == 'open-seminar' && numberTopicsNotAbleSeminar > 0 && <div>
                    <button type="button" class="btn btn-sm btn-margin btn-success" onClick={this.showAllSeminar}>{`Hiển thị các đề tài thực hiện dưới ${SEMINAR_DURATION} tháng`}
                    </button>
                    <hr />
                </div> }
                { type == 'open-seminar' && numberTopicsNotAbleSeminar == 0 && <div>
                    <button type="button" class="btn btn-sm btn-margin btn-success" onClick={this.filterOpenSeminar}>{`Không hiển thị các đề tài thực hiện dưới ${SEMINAR_DURATION} tháng`}</button>
                    <hr />
                </div> }
                {/* Modal content block */}
                <ManageTopicChange
                    type={type}
                    checked={checked}
                    topics={cur_topics}
                    degrees={degrees}
                    lecturers={lecturers}
                    handleCheck={this.handleCheck}
                    handleToggleCheckAll={this.handleToggleCheckAll}
                    error={error}
                    message={message} />
            </Modal>
        </div>
    }
}

export default AdminTopicsWorking
