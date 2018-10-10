import React, { Component } from 'react'
import { Modal, notify } from 'Components'

class ExpertiseModal extends Component {
    constructor(props) {
        super(props)
        this.state = {
            reviewStatus: 0,
            content: ''
        }
    }
    submitReview = e => {
        e.preventDefault()
        const { actions, topic, reloadData, uid } = this.props
        const { reviewStatus, content } = this.state
        const { reviews } = topic
        const listReviews = reviews.filter(r => r.officerId == uid)
        let currentIteration = 0
        listReviews.forEach(r => {
            currentIteration = r.iteration > currentIteration ? r.iteration : currentIteration
        })
        const data = {
            topicId: topic.id,
            topicStatus: topic.topicStatus,
            reviewStatus, content,
            iteration: currentIteration
        }
        actions.updateReview(data).then(res => {
            reloadData(this.cancelReview())
        }).catch(err => {
            this.cancelReview()
            notify.show(`Có lỗi xảy ra: ${err.ressponse.data.message}`, 'danger')
        })
    }
    cancelReview = () => {
        const { modalId } = this.props
        $(`#${modalId}`).modal('hide')
    }
    render() {
        const { modalId, title, topic } = this.props
        const { reviewStatus, content } = this.state
        return <Modal
            modalId={modalId}
            title={title}
            onSubmit={this.submitReview}
            onCancel={this.cancelReview}
            >
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
                <label class="col-sm-offset-1 col-sm-2 control-label">Ý kiến nhận xét</label>
                <div class="col-sm-8">
                    <textarea class="form-control" rows="10" value={content} onChange={e => this.setState({content: e.target.value})} />
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-offset-1 col-sm-2 control-label">Quyết định</label>
                <div class="col-sm-8">
                    <select class="form-control" value={reviewStatus} onChange={e => this.setState({reviewStatus: e.target.value})}>
                        <option value="0" disabled hidden></option>
                        <option value="1">Đạt</option>
                        <option value="3">Không đạt</option>
                        <option value="2">Cần chỉnh sửa</option>
                        <option value="4">Từ chối phản biện</option>
                    </select>
                </div>
            </div>
        </Modal>
    }
}

export default ExpertiseModal
