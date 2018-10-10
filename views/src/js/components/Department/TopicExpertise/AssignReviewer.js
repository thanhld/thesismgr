import React, { Component } from 'react'
import { Modal, notify } from 'Components'
import { DEPARTMENT_ASSIGN_REVIEWER } from 'Constants'
import { mailerActions } from 'Actions';

class AssignReviewer extends Component {
    constructor(props) {
        super(props)
        this.state = {
            reviewer: ['', '', ''],
            error: false,
            message: ''
        }
    }
    componentWillReceiveProps(nextProps) {
        const { expertiseOfficerIds } = nextProps.topic
        const reviewer = expertiseOfficerIds ? expertiseOfficerIds.split(',') : ['', '', '']
        this.setState({
            reviewer
        })
    }
    submitAssign = e => {
        e.preventDefault()
        const { reviewer } = this.state
        // Check same reviewer
        let isSameReviewer = false
        if (reviewer[0] && reviewer[0] == reviewer[1]) isSameReviewer = true
        if (reviewer[1] && reviewer[1] == reviewer[2]) isSameReviewer = true
        if (isSameReviewer) {
            this.setState({
                error: true,
                message: 'Giảng viên phản biện trùng nhau'
            })
            return false
        } else {
            this.setState({
                error: false
            })
        }

        const { actions, topic } = this.props
        const data = {
            stepId: 5003,
            topicId: topic.id,
            expertiseOfficerIds: reviewer.join()
        }
        actions.createActivity(DEPARTMENT_ASSIGN_REVIEWER, [data]).then(res => {
            const { data } = res.action.payload
            if (data.length == 0) {
                this.cancelAssign()
                mailerActions.reviewTopicMail();
                this.props.reloadData(() => {
                    notify.show(`Cập nhật giảng viên thẩm định thành công`, 'primary')
                })
            } else {
                this.cancelAssign()
                notify.show(`Có lỗi xảy ra: ${data[0].error}`, 'danger')
            }
        }).catch(err => {
            notify.show(`Có lỗi xảy ra: ${err.response.data.message}`, 'danger')
        })
    }
    cancelAssign = () => {
        const { modalId } = this.props
        $(`#${modalId}`).modal('hide')
    }
    handleReviewerChange = (e, pos) => {
        e.preventDefault()
        let reviewer = this.state.reviewer
        reviewer[pos] = e.target.value
        this.setState({
            reviewer
        })
    }
    render() {
        const { modalId, title, lecturers, degrees, topic } = this.props
        const { reviewer, error, message } = this.state
        const lecturerOption = lecturers.map(l => {
            if (l.role != 3 && l.role != 6) return false
            const degree = degrees.find(d => d.id == l.degreeId)
            let isDisabled = false
            if (topic.mainSupervisorId == l.id) isDisabled = true
            if (topic.coSupervisorIds == l.id) isDisabled = true
            return <option key={l.id} value={l.id} disabled={isDisabled}>{degree && `${degree.name}.`} {l.fullname}</option>
        })
        return <Modal
            modalId={modalId}
            title={title}
            onSubmit={this.submitAssign}
            onCancel={this.cancelAssign}>
            { error && <div>
                <div class="text-message-error">
                    Có lỗi xảy ra: { message }
                </div>
                <br />
            </div> }
            <div class="form-group">
                <label class="col-sm-offset-1 col-sm-2 control-label">Tên đề tài</label>
                <div class="col-sm-8">
                    <p class="form-control-static">{ topic.isEnglish ? topic.englishTopicTitle : topic.vietnameseTopicTitle }</p>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-offset-1 col-sm-2 control-label">Học viên thực hiện</label>
                <div class="col-sm-8">
                    <p class="form-control-static">{ topic.learner && topic.learner.fullname }</p>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-offset-1 col-sm-2 control-label">Phản biện 1</label>
                <div class="col-sm-8">
                    <select class="form-control" value={reviewer[0]} onChange={e => this.handleReviewerChange(e, 0)}>
                        <option value="">Không có</option>
                        { lecturerOption }
                    </select>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-offset-1 col-sm-2 control-label">Phản biện 2</label>
                <div class="col-sm-8">
                    <select class="form-control" value={reviewer[1]} onChange={e => this.handleReviewerChange(e, 1)}>
                        <option value="">Không có</option>
                        { lecturerOption }
                    </select>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-offset-1 col-sm-2 control-label">Phản biện 3</label>
                <div class="col-sm-8">
                    <select class="form-control" value={reviewer[2]} onChange={e => this.handleReviewerChange(e, 2)}>
                        <option value="">Không có</option>
                        { lecturerOption }
                    </select>
                </div>
            </div>
        </Modal>
    }
}

export default AssignReviewer
