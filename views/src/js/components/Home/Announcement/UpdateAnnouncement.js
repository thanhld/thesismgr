import React, { Component } from 'react'
import moment from 'moment'
import DatePicker from 'react-datepicker'
import { notify } from 'Components'
import { formatDate } from 'Helper'

class UpdateAnnoucement extends Component {
    constructor() {
        super()
        this.state = {
            error: false,
            message: '',
            current_announce: {},
        }
    }
    componentWillReceiveProps(nextProps) {
        const { action, announcement } = nextProps
        if (action == "create") {
            this.setState({
                error: false,
                current_announce: {
                    ...announcement,
                    showDate: moment(),
                    hideDate: moment().add(1, 'week')
                }
            })
        }
        if (action == "update") {
            this.setState({
                error: false,
                current_announce: {
                    ...announcement,
                    showDate: moment(new Date(announcement.showDate || Date.now())),
                    hideDate: moment(new Date(announcement.hideDate || Date.now()))
                }
            })
        }
    }
    reloadData = () => {
        const { actions, modalId } = this.props
        actions.loadAnnouncements().then(() => {
            $(`#${modalId}`).modal('hide')
        })
    }
    handleSubmit = e => {
        e.preventDefault()
        const { action, actions, modalId, reloadData } = this.props
        const { current_announce } = this.state
        const { showDate, hideDate } = current_announce
        const data = {
            ...this.state.current_announce,
            showDate: showDate.format('YYYY-MM-DD HH:mm:ss'),
            hideDate: hideDate.format('YYYY-MM-DD HH:mm:ss')
        }
        let tmp = ''
        if (action == 'create') {
            actions.createAnnouncement(data)
            tmp = `Thông báo đã được tạo thành công`
        } else if (action == 'update') {
            actions.updateAnnouncement(data, data.id)
            tmp = `Thông báo đã được cập nhật thành công`
        }
        reloadData(() => {
            $(`#${modalId}`).modal('hide')
            notify.show(tmp, 'primary')
        })
    }
    handleShowdateChange = date => {
        this.setState({
            current_announce: {
                ...this.state.current_announce,
                showDate: date
            }
        })
    }
    handleHidedateChange = date => {
        this.setState({
            current_announce: {
                ...this.state.current_announce,
                hideDate: date
            }
        })
    }
    render() {
        const { action, modalId } = this.props
        const { error, message, current_announce } = this.state
        return (
            <div id={`${modalId}`} class="modal fade" tabIndex="-1" role="dialog" aria-labelledby="announcementModal">
                <div class="modal-dialog modal-lg" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                            <h4 class="modal-title" id="announcementModal">{ action == "create" ? "Thêm thông báo" : "Cập nhật thông báo" }</h4>
                        </div>
                        <form class="form-horizontal" onSubmit={this.handleSubmit}>
                            <div class="modal-body">
                                { error &&
                                    <div>
                                        <div class="text-message-error">Có lỗi xảy ra: {message}</div>
                                        <br />
                                    </div>
                                }
                                <div class="form-group">
                                    <label class="col-sm-offset-1 col-sm-2 control-label">Tên thông báo (*)</label>
                                    <div class="col-sm-8">
                                        <input type="text" class="form-control" value={current_announce.title || ''} onChange={e => this.setState({current_announce: {...current_announce, title: e.target.value}})} required />
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-offset-1 col-sm-2 control-label">Nội dung</label>
                                    <div class="col-sm-8">
                                        <textarea class="form-control" rows="8" value={current_announce.content || ''} onChange={e => this.setState({current_announce: {...current_announce, content: e.target.value}})} />
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-offset-1 col-sm-2 control-label">Ngày hiện (*)</label>
                                    <div class="col-sm-8">
                                        <DatePicker
                                            className="form-control"
                                            selected={current_announce.showDate}
                                            onChange={this.handleShowdateChange}
                                            required
                                        />
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-offset-1 col-sm-2 control-label">Ngày ẩn (*)</label>
                                    <div class="col-sm-8">
                                        <DatePicker
                                            className="form-control"
                                            selected={current_announce.hideDate}
                                            onChange={this.handleHidedateChange}
                                            required
                                        />
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-offset-1 col-sm-2 control-label">Tags</label>
                                    <div class="col-sm-8">
                                        <input type="text" class="form-control" value={current_announce.tags || ''} onChange={e => this.setState({current_announce: {...current_announce, tags: e.target.value}})} />
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="submit" class="btn btn-primary btn-margin">
                                    {action == "create" ? "Tạo mới" : "Cập nhật"}
                                </button>
                                <button type="button" class="btn btn-default" data-dismiss="modal">Bỏ qua</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        )
    }
}

export default UpdateAnnoucement
