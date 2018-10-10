import React, { Component } from 'react'
import { Modal, notify } from 'Components'
import { LECTURER_EDIT_TOPIC } from 'Constants'

class LecturerEditTopic extends Component {
    constructor(props) {
        super(props)
        this.state = {
            oldTopic: {},
            topic: {
                id: '',
                vietnameseTopicTitle: '',
                isEnglish: false,
                englishTopicTitle: '',
                tags: '',
                description: ''
            }
        }
    }
    componentWillReceiveProps(nextProps) {
        const { id, vietnameseTopicTitle, isEnglish, englishTopicTitle, tags, description } = nextProps.topic
        const nextTopic = { id, vietnameseTopicTitle, isEnglish, englishTopicTitle, tags, description }
        this.setState({
            oldTopic: nextTopic,
            topic: nextTopic
        })
    }
    handleChange = e => {
        const type = e.target.type
        const name = e.target.name
        const value = type == 'checkbox' ? e.target.checked : e.target.value
        this.setState({
            topic: {
                ...this.state.topic,
                [name]: value
            }
        })
    }
    submitEditTopic = e => {
        e.preventDefault()
        const { actions, reloadData, uid } = this.props
        const { topic, oldTopic } = this.state
        let newTopic = {...topic}
        Object.keys(newTopic).forEach(key => {
            if (newTopic[key] == oldTopic[key]) delete newTopic[key]
        })
        actions.createActivity(LECTURER_EDIT_TOPIC, [{
            stepId: 4002,
            topicId: oldTopic.id,
            requestedSupervisorId: uid,
            data: newTopic
        }]).then(res => {
            const { data } = res.action.payload
            if (data.length == 0) {
                reloadData(() => {
                    this.cancelEditTopic()
                    notify.show(`Thầy/cô đã điều chỉnh đề tài thành công`, 'primary')
                })
            } else {
                this.cancelEditTopic()
                notify.show(`Có lỗi xảy ra: ${data[0].error}`, 'danger')
            }
        }).catch(err => {
            this.cancelEditTopic()
            notify.show(`Có lỗi xảy ra: ${err.response.data.message}`, 'danger')
        })
    }
    cancelEditTopic = () => {
        $(`#lecturerEditTopic`).modal('hide')
    }
    render() {
        const { topic } = this.state
        const { vietnameseTopicTitle, isEnglish, englishTopicTitle, description, tags } = topic
        return <Modal
            modalId="lecturerEditTopic"
            title="Chỉnh sửa đề tài"
            onSubmit={this.submitEditTopic}
            onCancel={this.cancelEditTopic}
            >
            <div class="form-group">
                <label class="col-xs-offset-1 col-xs-2 control-label">Tên tiếng Việt</label>
                <div class="col-xs-8">
                    <input name="vietnameseTopicTitle" type="text" class="form-control" value={vietnameseTopicTitle} onChange={this.handleChange} />
                </div>
            </div>
            { isEnglish == 1  && <div class="form-group">
                <label class="col-xs-offset-1 col-xs-2 control-label">Tên tiếng Anh</label>
                <div class="col-xs-8">
                    <input name="englishTopicTitle" type="text" class="form-control" value={englishTopicTitle} onChange={this.handleChange} />
                </div>
            </div> }
            <div class="form-group">
                <label class="col-xs-offset-1 col-xs-2 control-label">Thực hiện tiếng Anh?</label>
                <div class="col-xs-8">
                    <input name="isEnglish" type="checkbox" checked={isEnglish} onChange={this.handleChange} />
                </div>
            </div>
            <div class="form-group">
                <label class="col-xs-offset-1 col-xs-2 control-label">Mô tả</label>
                <div class="col-xs-8">
                    <textarea name="description" rows="5" class="form-control" value={description} onChange={this.handleChange} />
                </div>
            </div>
            <div class="form-group">
                <label class="col-xs-offset-1 col-xs-2 control-label">Tags</label>
                <div class="col-xs-8">
                    <input name="tags" type="text" class="form-control" value={tags} onChange={this.handleChange} />
                </div>
            </div>
        </Modal>
    }
}

export default LecturerEditTopic
