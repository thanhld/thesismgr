import React, { Component } from 'react'
import moment from 'moment'
import DatePicker from 'react-datepicker'
import { Modal, notify } from 'Components'
import { formatMoment } from 'Helper'
import { LEARNER_CHANGE_TOPIC } from 'Constants'
import { mailerActions } from 'Actions';

const REQUEST = {
    'extend': {
        title: 'Xin gia hạn thời gian thực hiện đề tài',
        size: 'modal-md'
    },
    'pause': {
        title: 'Xin tạm hoãn thời gian thực hiện đề tài',
        size: 'modal-md'
    },
    'cancel': {
        title: 'Xin thôi thực hiện đề tài',
        size: 'modal-md'
    },
    'defense': {
        title: 'Xin đăng ký bảo vệ đề tài',
        size: 'modal-md'
    },
    'seminar': {
        title: 'Xin đăng ký báo cáo tiến độ đề tài',
        size: 'modal-md'
    }
}

class RequestModal extends Component {
    constructor(props) {
        super(props)
        this.state = {
            delayDuration: '',
            startPauseDate: moment(),
            pauseDuration: '',
            cancelReason: ''
        }
    }
    changeDate = date => {
        this.setState({
            startPauseDate: date
        })
    }
    handleChange = e => {
        const name = e.target.name
        const value = e.target.value
        this.setState({
            [name]: value
        })
    }
    submitRequest = e => {
        e.preventDefault()
        const { delayDuration, startPauseDate, pauseDuration, cancelReason } = this.state
        const { actions, requestType, supervisor, topicId } = this.props
        let data = {}
        let stepId = 0
        if (requestType == 'extend') {
            data = {
                delayDuration: parseInt(delayDuration)
            }
            stepId = 102
        } else if (requestType == 'pause') {
            data = {
                startPauseDate: formatMoment(startPauseDate),
                pauseDuration: parseInt(pauseDuration)
            }
            stepId = 104
        } else if (requestType == 'cancel') {
            data = {
                cancelReason
            }
            stepId = 103
        } else if (requestType == 'defense') {
            stepId = 105
        } else if (requestType == 'seminar') {
            stepId = 106
        }
        let requestData = {
            stepId,
            topicId
        }
        if (requestType != 'defense' && requestType != 'seminar') requestData['data'] = data
        actions.createActivity(LEARNER_CHANGE_TOPIC, [requestData]).then(res => {
            const { data } = res.action.payload
            if (data.length == 0) {
                mailerActions.approveTopicMail();
                this.props.reloadTopic(() => {
                    this.dismissModal()
                    notify.show('Đã gửi yêu cầu thành công', 'primary')
                })
            } else {
                this.dismissModal()
                notify.show(`Có lỗi xảy ra: ${data[0].error}`, 'danger')
            }
        }).catch(err => {
            notify.show(`Có lỗi xảy ra: ${err.response.data.message}`, 'danger')
        })
    }
    dismissModal = () => {
        //console.log($("#requestChange"));
        $('#requestChange').modal('hide');
        $('body').removeClass("modal-open");
        $('.modal-backdrop').remove();
    }
    render() {
        const { delayDuration, startPauseDate, pauseDuration, cancelReason } = this.state
        const { requestType } = this.props
        return <Modal
            modalId="requestChange"
            title={REQUEST[requestType].title}
            size={REQUEST[requestType].size}
            onSubmit={this.submitRequest}
            onCancel={this.dismissModal}>
            { requestType == 'extend' && <div>
                <div class="form-group">
                    <label class="col-sm-4 control-label">Thời gian gia hạn</label>
                    <div class="col-sm-8">
                        <input name="delayDuration" type="number" min={1} max={60} class="form-control" placeholder="Nhập số tháng muốn xin gia hạn" value={delayDuration} onChange={this.handleChange} required />
                    </div>
                </div>
                <div class="alert alert-info">Bạn có thể hủy yêu cầu trước khi Khoa xác nhận.</div>
            </div> }
            { requestType == 'pause' && <div>
                <div class="form-group">
                    <label class="col-sm-4 control-label">Tạm hoãn từ ngày</label>
                    <div class="col-sm-8">
                        <DatePicker className="form-control" selected={startPauseDate} onChange={this.changeDate} required />
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-4 control-label">Thời gian tạm hoãn</label>
                    <div class="col-sm-8">
                        <input name="pauseDuration" type="number" min={1} max={60} class="form-control" placeholder="Nhập số tháng muốn tạm hoãn" value={pauseDuration} onChange={this.handleChange} required />
                    </div>
                </div>
                <div class="alert alert-info">Bạn có thể hủy yêu cầu trước khi Khoa xác nhận.</div>
            </div> }
            { requestType == 'cancel' && <div>
                <div class="row">
                    <div class="col-sm-12">
                        <label class="control-label">Lí do xin thôi</label>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-12">
                        <textarea rows={8} name="cancelReason" type="text" class="form-control" value={cancelReason} onChange={this.handleChange} />
                    </div>
                </div>
            </div> }
            { requestType == 'defense' && <div>
                Học viên xin đăng ký bảo vệ đề tài.
            </div> }
            { requestType == 'seminar' && <div>
                Học viên xin đăng ký báo cáo tiến độ đề tài.
            </div> }
        </Modal>
    }
}

export default RequestModal
