import React, { Component } from 'react'
import { Modal, notify } from 'Components'
import ManageTopicChange from '../ManageTopicChange'
import { routeName } from 'Config'
import { array } from 'Helper'
import {
    ADMIN_CLOSE_TOPIC_SEMINAR,
    ADMIN_CLOSE_REGISTER_SEMINAR } from 'Constants'
import { mailerActions } from 'Actions';

class TopicsSeminar extends Component {
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
    filterCloseSeminar = () => {
        const { topics } = this.props
        const tmp = topics.filter(t => t.topicStatus == 899)
        this.setState({
            error: false,
            title: 'Ghi nhận đã kiểm tra tiến độ',
            cur_topics: tmp,
            checked: array(tmp.length, () => true),
            type: 'close-seminar'
        })
    }
    filterToOpenRegister = () => {
        const { topics } = this.props
        const tmp = topics.filter(t => t.topicStatus == 900)
        this.setState({
            error: false,
            title: 'Mở quyền đăng ký bảo vệ',
            cur_topics: tmp,
            checked: array(tmp.length, () => true),
            type: 'open-defense'
        })
    }
    closeRegisterSeminar = () => {
        const { topics } = this.props
        const tmp = topics.filter(t => t.topicStatus == 897)
        this.setState({
            error: false,
            title: 'Đóng quyền đăng ký kiểm tra tiến độ',
            cur_topics: tmp,
            checked: array(tmp.length, () => true),
            type: 'close-register-seminar'
        }, () => {
            this.handleSubmit()
        })
    }
    handleSubmit = e => {
        e.preventDefault()
        const { checked, cur_topics, type } = this.state
        const { actions, topics, reloadData } = this.props
        const stepId = type == 'close-seminar' ? 2091
            : type == 'open-defense' ? 2053
            : type == 'close-register-seminar' ? 2092
            : 0
        if (type == 'close-seminar' || type == 'close-register-seminar') {
            // Calculate checkedTopicIds
            let checkedTopicIds = []
            checked.forEach((c, index) => {
                if (!c) return
                checkedTopicIds.push(cur_topics[index].id)
            })
            checkedTopicIds = checkedTopicIds.join()
            // Create activity data to submit
            let topicData = []
            topics.forEach(t => {
                topicData.push({
                    stepId,
                    topicId: t.id,
                    checkedTopicIds
                })
            })
            // Submit activity
            actions.createActivity(type == 'close-seminar' ? ADMIN_CLOSE_TOPIC_SEMINAR : ADMIN_CLOSE_REGISTER_SEMINAR, topicData).then(res => {
                const { data } = res.action.payload
                if (data.length > 0) {
                    const mess = data.map(d => {
                        const t = topics.find(t => d.topicId == t.id)
                        const e = d.error
                        return `${t && t.learner.learnerCode}: ${e}`
                    })
                    this.setState({
                        error: true,
                        message: mess.join('; ')
                    })
                } else {
                    this.props.reloadData(() => {
                        $('#topic-seminar').modal('hide')
                        notify.show('Thầy/cô cập nhật đề tài thành công', 'primary')
                    })
                }
            }).catch(err => {
                notify.show(`Có lỗi xảy ra: ${err.response.data.message}`, 'danger')
            })
        } else if (type == 'open-defense') {
            let topicData = []
            checked.forEach((c, index) => {
                if (!c) return
                topicData.push({
                    stepId,
                    topicId: cur_topics[index].id
                })
            })
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
                        $('#topic-seminar').modal('hide')
                        notify.show('Thầy/cô cập nhật đề tài thành công', 'primary')
                    })
                }
                mailerActions.protectTopicMail();
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
    render() {
        const { checked, cur_topics, title, error, message } = this.state
        const { topics, degrees, lecturers, type } = this.props
        return <div class="topic-admin-header">
            <button class="btn btn-sm btn-margin btn-success" data-toggle="modal" data-target="#topic-seminar" onClick={this.filterCloseSeminar}>Ghi nhận đã kiểm tra tiến độ</button>
            { this.props.type != routeName['ADMIN_TOPIC_STUDENT'] && <button class="btn btn-sm btn-margin btn-success margin-right" onClick={this.closeRegisterSeminar}>Đóng quyền đăng ký kiểm tra tiến độ</button> }
            <button class="btn btn-sm btn-margin btn-success" data-toggle="modal" data-target="#topic-seminar" onClick={this.filterToOpenRegister}>Mở quyền đăng ký bảo vệ</button>
            <button class="btn btn-sm btn-margin btn-success" onClick={e => this.props.updateNeedPrint(true)}>In danh sách</button>
            <Modal
                modalId="topic-seminar"
                title={title}
                onSubmit={this.handleSubmit}
                onCancel={this.handleCancel}>
                <ManageTopicChange
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

export default TopicsSeminar
