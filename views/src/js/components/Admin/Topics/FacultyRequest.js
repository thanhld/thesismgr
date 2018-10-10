import React, { Component } from 'react'
import moment from 'moment'
import { notify } from 'Components'
import { generateDocx } from 'GenDocx'
import { ADMIN_FACULTY_REQUEST } from 'Constants'

const array = (n, func) => {
    return Array(n).fill().map(x => func())
}

class FacultyRequest extends Component {
    constructor(props) {
        super(props)
        this.state = {
            checked: [],
            error: false,
            message: '',
            documentNum: '',
            isGenDocx: true
        }
    }
    componentWillReceiveProps(nextProps) {
        if (nextProps.topics.length > 0) {
            this.setState({
                checked: array(nextProps.topics.length, () => true)
            })
        }
    }
    getLecturerNameById = id => {
        const lecturer = this.props.lecturers.find(l => l.id == id)
        const degree = lecturer && this.props.degrees.find(d => d.id == lecturer.degreeId)
        if (lecturer) return `${degree && `${degree.name}.`} ${lecturer.fullname}`
        else return false
    }
    getCoSupervisorsNames = id => {
        const { lecturers, degrees } = this.props
        const coSupervisorsList = id ? id.split(',') : []
        return coSupervisorsList.map(c => this.getLecturerNameById(c)).join(`\n`)
    }
    getSupervisorOffices = (...ids) => {
        var offices = [];
        ids.map(id => {
            const lecturer = this.props.lecturers.find(l => l.id == id)
            if(lecturer) {
                if(lecturer.departmentType == 3) {  //Out office
                    offices.push(lecturer.departmentName)
                } else {
                    offices.push("Trường ĐHCN, ĐHQGHN")
                }
            }
        })
        return offices.map(o => o).join(". ")
    }
    dismissModal = () => {
        const { modalId } = this.props
        this.setState({
            error: false
        })
        $(`#${modalId}`).modal('hide')
    }
    handleToggleCheckAll = e => {
        const length = this.state.checked.length
        this.setState({
            checked: array(length, () => e.target.checked)
        })
    }
    handleCheck = index => e => {
        let newCheck = this.state.checked.slice()
        newCheck[index] = !newCheck[index]
        this.setState({
            checked: newCheck
        })
    }
    handleSubmit = e => {
        e.preventDefault()
        const { checked, documentNum } = this.state
        const { actions, topics, lecturers, degrees, reloadData } = this.props
        const { templates, subtitle, stepId } = this.props
        let docData = []
        let count = 0
        checked.forEach((c, index) => {
            if (c == false) return
            const topicId = topics[index].id
            const topic = topics.find(topic => topic.id == topicId)
            const { topicChange } = topic
            const tName = topic.isEnglish ? topic.englishTopicTitle : topic.vietnameseTopicTitle
            const mainSupervisor = this.getLecturerNameById(topic.mainSupervisorId)
            const coSupervisors = this.getCoSupervisorsNames(topic.coSupervisorIds)
            let docTopicItem = {
                index: count + 1,
                learnerCode: topic.learner.learnerCode,
                learnerName: topic.learner.fullname,
                learnerCourse: topic.learner.trainingCourseCode,
                topicName: tName,
                mainSupervisor,
                coSupervisors
            }
            if (templates == 'edit') {
                docTopicItem['topicNewName'] = (topicChange.isEnglish ? topicChange.englishTopicTitle : topicChange.vietnameseTopicTitle) || ''
                docTopicItem['oldSupervisors'] = mainSupervisor + (coSupervisors && `\n${coSupervisors}`)
                const newMainName = this.getLecturerNameById(topicChange.mainSupervisorId)
                const newCosName = this.getCoSupervisorsNames(topicChange.coSupervisorIds)
                docTopicItem['newSupervisors'] = (`${newMainName ? newMainName : mainSupervisor}${newCosName ? `\n${newCosName}`: coSupervisors && `\n${coSupervisors}`}`)
                if (docTopicItem['newSupervisors'] == docTopicItem['oldSupervisors']) docTopicItem['newSupervisors'] = ''
            } else if (templates == 'extend') {
                docTopicItem['delayDuration'] = topicChange.delayDuration
            } else if (templates == 'cancel') {
                docTopicItem['cancelReason'] = topicChange.cancelReason
            } else if (templates == 'pause') {
                docTopicItem['startPauseDate'] = moment(topicChange.startPauseDate).format('L')
                docTopicItem['pauseDuration'] = topicChange.pauseDuration
            } else if (templates == 'register' || templates == 'defense') {
                docTopicItem['offices'] = this.getSupervisorOffices(topic.mainSupervisorId,topic.coSupervisorIds)
            }
            count++
            docData.push(docTopicItem)
        })
        const createResponseActivity = documentId => {
            let topicData = []
            checked.forEach((c, index) => {
                if (c == false) return
                topicData.push({
                    stepId,
                    documentId,
                    topicId: topics[index].id
                })
            })
            if (topicData.length == 0) {
                this.setState({
                    error: true,
                    message: 'Không có đề tài nào được chọn.'
                })
                return false
            }
            actions.createActivity(ADMIN_FACULTY_REQUEST, topicData).then(res => {
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
                    reloadData()
                    this.dismissModal()
                    notify.show('Thầy/cô đã cập nhật đề tài thành công', 'primary')
                }
            })
        }

        let docObj = {
            subtitle,
            topics: docData
        }
        generateDocx(`/templates/${templates}.docx`, docObj, `${documentNum}.docx`, actions, createResponseActivity)
    }
    render() {
        const { modalId, topics, lecturers, degrees, title } = this.props
        const { checked, error, message, documentNum, isGenDocx } = this.state
        return <div id={modalId} class="modal fade" tabIndex="-1" role="dialog" aria-labelledby="FacultyResponse">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close" onClick={this.dismissModal}>
                            <span aria-hidden="true">&times;</span>
                        </button>
                        <h4 class="modal-title" id="FacultyResponse">{title}</h4>
                    </div>
                    <form class="form-horizontal" onSubmit={this.handleSubmit}>
                        <div class="modal-body">
                            { error && <div>
                                <div class="text-message-error">
                                    Đề tài chưa được lưu thành công: { message }
                                </div>
                                <br />
                            </div> }
                            <div class="form-group">
                                <label class="col-xs-offset-1 col-xs-2 control-label">Số tờ trình (*)</label>
                                <div class="col-xs-8">
                                    <input type="text" value={documentNum} class="form-control" onChange={e => this.setState({documentNum : e.target.value})} required />
                                </div>
                            </div>
                            <br />
                            <div class="text-center">
                                Danh sách đề tài được đề nghị
                            </div>
                            <div class="table-responsive review-table-container">
                                <table class="table table-hover table-condensed">
                                    <thead>
                                        <tr>
                                            <th class="col-xs-1">
                                                <input
                                                    type="checkbox"
                                                    checked={!checked.includes(false)}
                                                    onChange={this.handleToggleCheckAll}
                                                />
                                            </th>
                                            <th class="col-xs-2">Tên học viên</th>
                                            <th class="col-xs-6">Tên đề tài</th>
                                            <th class="col-xs-3">GVHD chính</th>
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
                            <br />
                            {/*<div class="form-group">
                                <div class="col-sm-offset-2 col-sm-8">
                                    <input class="margin-right" type="checkbox" checked={isGenDocx} onChange={e => { this.setState({isGenDocx: e.target.checked}) }} />
                                    <i>Tự động xuất tờ trình</i>
                                </div>
                            </div>*/}
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

export default FacultyRequest
