import React, { Component } from 'react'
import moment from 'moment'
import DatePicker from 'react-datepicker'
import { notify } from 'Components'
import { formatMoment, topicTypeToName } from 'Helper'
import { ADMIN_FACULTY_RESPONSE } from 'Constants'

const array = (n, func) => {
    return Array(n).fill().map(x => func())
}

class FacultyResponse extends Component {
    constructor(props) {
        super(props)
        this.state = {
            checked: [],
            error: false,
            message: '',
            documentNum: '',
            createdDate: moment(),
            startDate: moment(),
            deadlineDate: moment().add(1, 'month')
        }
    }
    componentWillReceiveProps(nextProps) {
        if (nextProps.topics.length > 0) {
            this.setState({
                checked: array(nextProps.topics.length, () => true)
            })
        }
    }
    dismissModal = () => {
        const { modalId } = this.props
        this.setState({
            error: false
        })
        $(`#${modalId}`).modal('hide')
    }
    handleChange = e => {
        const name = e.target.name
        const value = e.target.value
        this.setState({
            [name]: value
        })
    }
    handleToggleCheckAll = e => {
        const length = this.state.checked.length
        this.setState({
            checked: array(length, () => e.target.checked)
        })
    }
    handleCheck = index => e => {
        var newCheck = this.state.checked.slice()
        newCheck[index] = !newCheck[index]
        this.setState({
            checked: newCheck
        })
    }
    submitFile = callback => {
        const { actions } = this.props;
        var files = document.getElementById('uploadFile').files
        var formData = new FormData()
        formData.append('uploadFile', files[0], files[0].name)
        actions.uploadFile(formData).then(response => {
            callback(response.action.payload.data.url)
        }).catch(err => {
            this.setState({
                error: true,
                message: 'Không upload được file'
            })
        })
    }
    handleSubmit = e => {
        e.preventDefault()
        const { checked, documentNum, createdDate, startDate, deadlineDate } = this.state
        const { actions, topics, stepId, reloadData } = this.props
        const createSubmit = fileUrl => {
            const createActivity = documentId => {
                let topicData = []
                checked.forEach((c, index) => {
                    if (c == false) return
                    topicData.push({
                        stepId,
                        documentId,
                        topicId: topics[index].id,
                        data: {
                            startDate: formatMoment(startDate),
                            deadlineDate: formatMoment(deadlineDate),
                            defaultDeadlineDate: formatMoment(deadlineDate)
                        }
                    })
                })
                actions.createActivity(ADMIN_FACULTY_RESPONSE, topicData).then(res => {
                    const { data } = res.action.payload
                    if (data.length > 0) {
                        const mess = data.map(d => {
                            const t = topics.filter(t => d.topicId == t.id)
                            const e = d.error
                            return `${t && t[0].learner.learnerCode}: ${e}`
                        })
                        this.setState({
                            error: true,
                            message: mess.join('; ')
                        })
                    } else {
                        reloadData()
                        this.dismissModal()
                        notify.show('Thầy/cô đã cập nhật đề tài thành công', 'primary')
                    }
                }).catch(err => {
                    this.setState({
                        error: true,
                        message: err.response.data.message
                    })
                })
            }
            actions.createDocument(
                documentNum, documentNum, fileUrl, formatMoment(createdDate)
            ).then(res => {
                createActivity(res.action.payload.data.documentId)
            }).catch(err => {
                notify.show(err.response.data.message, 'danger')
            })
        }

        let files = document.getElementById('uploadFile').files;
        if (files.length > 0) {
            this.submitFile(fileUrl => {
                createSubmit(fileUrl)
            })
        } else {
            createSubmit()
        }
    }
    render() {
        const { modalId, topics, lecturers, degrees, type } = this.props
        const { checked, error, message, documentNum, createdDate, startDate, deadlineDate } = this.state
        return <div id={modalId} class="modal fade" tabIndex="-1" role="dialog" aria-labelledby="FacultyResponse">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close" onClick={this.dismissModal}>
                            <span aria-hidden="true">&times;</span>
                        </button>
                        <h4 class="modal-title" id="FacultyResponse">Ghi nhận quyết định công nhận đề tài {topicTypeToName(type)}</h4>
                    </div>
                    <form class="form-horizontal" autoComplete="off" encType="multipart/form-data" onSubmit={this.handleSubmit}>
                        <div class="modal-body">
                            { error && <div>
                                <div class="text-message-error">
                                    Các đề tài chưa được lưu thành công: { message }
                                </div>
                                <br />
                            </div> }
                            <div class="form-group">
                                <label class="col-xs-offset-1 col-xs-2 control-label">Số quyết định (*)</label>
                                <div class="col-xs-8">
                                    <input name="documentNum" type="text" value={documentNum} class="form-control" onChange={this.handleChange} required />
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-xs-offset-1 col-xs-2 control-label">Ngày quyết định (*)</label>
                                <div class="col-xs-8">
                                    <DatePicker
                                        className="form-control"
                                        selected={createdDate}
                                        onChange={date => { this.setState({createdDate: date}) }}
                                        required
                                    />
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-xs-offset-1 col-xs-2 control-label">Toàn văn quyết định</label>
                                <div class="col-xs-8">
                                    <input id="uploadFile" type="file" class="form-control" />
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-xs-offset-1 col-xs-2 control-label">Ngày bắt đầu thực hiện (*)</label>
                                <div class="col-xs-8">
                                    <DatePicker
                                        className="form-control"
                                        selected={startDate}
                                        onChange={date => { this.setState({startDate: date}) }}
                                        required
                                    />
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-xs-offset-1 col-xs-2 control-label">Hạn cuối (*)</label>
                                <div class="col-xs-8">
                                    <DatePicker
                                        className="form-control"
                                        selected={deadlineDate}
                                        onChange={date => { this.setState({deadlineDate: date}) }}
                                        required
                                    />
                                </div>
                            </div>
                            <br />
                            <div class="text-center">
                                Danh sách đề tài được phê duyệt
                            </div>
                            <div class="table-responsive review-table-container">
                                <table class="table table-hover table-condensed">
                                    <thead>
                                        <tr>
                                            <th>
                                                <input
                                                    type="checkbox"
                                                    checked={!checked.includes(false)}
                                                    onChange={this.handleToggleCheckAll}
                                                />
                                            </th>
                                            <th>Tên học viên</th>
                                            <th>Tên đề tài</th>
                                            <th>GVHD chính</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        { topics.length > 0 && topics.map((topic, index) => {
                                            const lecturer = lecturers.find(l => l.id == topic.mainSupervisorId)
                                            const degree = lecturer && degrees.find(d => d.id == lecturer.degreeId)
                                            return (
                                                <tr key={topic.id}>
                                                    <td>
                                                        <input type="checkbox" checked={!!checked[index]} onChange={this.handleCheck(index)} />
                                                    </td>
                                                    <td>{topic.learner && topic.learner.fullname}</td>
                                                    <td>{topic.isEnglish ? topic.englishTopicTitle : topic.vietnameseTopicTitle}</td>
                                                    <td>{degree && `${degree.name}.`} {lecturer && lecturer.fullname}</td>
                                                </tr>
                                            )})}
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="submit" class="btn btn-primary">Đồng ý</button>
                            <button type="button" class="btn btn-default" data-dismiss="modal" onClick={this.dismissModal}>Bỏ qua</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    }
}

export default FacultyResponse
