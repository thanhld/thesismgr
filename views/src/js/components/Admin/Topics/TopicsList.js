import React, { Component } from 'react'
import Pagination from 'react-js-pagination'
import moment from 'moment'
import { ITEM_PER_PAGE, PAGE_RANGE } from 'Config'
import { Link } from 'react-router'
import { notify } from 'Components'
import { OFFICER_LECTURER_ROLE } from 'Constants'
import { getNextAdminDeclineStep, getNewSupervisorIds, arrayRange } from 'Helper'
import { ADMIN_FACULTY_ALLOW_EDIT, ADMIN_FACULTY_DECLINE, TOPIC_STATUS, ADMIN_CHECK_PAPER, ADMIN_FINISH_TOPIC } from 'Config'
import { generateDocx } from 'GenDocx'

const canCancelRequest = status => {
    if (status >= 891 && status <= 892) return true
    if (status >= 894 && status <= 895) return true
    if (status >= 201 && status <= 202) return true
    if (status >= 301 && status <= 302) return true
    if (status >= 668 && status <= 670) return true
    return false
}

class TopicsList extends Component {
    constructor(props) {
        super(props)
        this.state = {
            filDepart: '',
            filLec: '',
            filStatus: ''
        }
    }
    componentWillReceiveProps(nextProps) {
        const { needPrint } = this.props

        if (!needPrint && nextProps.needPrint) {
            this.printTopics()
            this.props.updateNeedPrint(false)
        }
    }
    deleteTopic = id => () => {
        const val = confirm('Thầy/cô có chắc chắn xóa đề tài này?')
        if (!val) return
        this.props.actions.deleteTopic(id).then(() => {
            this.props.reloadData(() => {
                notify.show(`Thầy/cô đã xóa đề tài thành công`, 'primary')
            })
        }).catch(err => {
            notify.show(`Có lỗi xảy ra: ${err.response.data.message}`, 'danger')
        })
    }
    declineTopic = (topic, stepId) => {
        const val = confirm('Thầy/cô có chắc chắn từ chối đề tài này?')
        const { actions, lecturers, reloadData } = this.props
        if (!val) return
        let data = {
            stepId,
            topicId: topic.id
        }
        if (stepId == 2010) { // Admin decline request change
            const newSupervisorIds = getNewSupervisorIds('main', topic, lecturers)
            const oldSupervisorIds = getNewSupervisorIds('sub', topic, lecturers)
            data['newSupervisorIds'] = newSupervisorIds
            data['oldSupervisorIds'] = oldSupervisorIds
        }
        actions.createActivity(ADMIN_FACULTY_DECLINE, [data]).then(res => {
            const { data } = res.action.payload
            if (data.length == 0) {
                reloadData(() => {
                    notify.show(`Thầy/cô đã từ chối yêu cầu thành công`, 'primary')
                })
            } else {
                notify.show(`Có lỗi xảy ra: ${data[0].error}`, 'danger')
            }
        }).catch(err => {
            notify.show(`Có lỗi xảy ra: ${err.response.data.message}`, 'danger')
        })
    }
    allowEditTopic = (topic, stepId) => {
        const val = confirm('Thầy/cô có chắc chắn cho phép sửa đổi đề tài này?')
        const { actions, lecturers, reloadData } = this.props
        if (!val) return
        let data = {
            stepId,
            topicId: topic.id
        }
        actions.createActivity(ADMIN_FACULTY_ALLOW_EDIT, [data]).then(res => {
            const { data } = res.action.payload
            if (data.length == 0) {
                reloadData(() => {
                    notify.show(`Thầy/cô đã cập nhật đề tài thành công`, 'primary')
                })
            } else {
                notify.show(`Có lỗi xảy ra: ${data[0].error}`, 'danger')
            }
        }).catch(err => {
            notify.show(`Có lỗi xảy ra: ${err.response.data.message}`, 'danger')
        })
    }
    changeCheckPaper = topic => {
        if (topic.topicStatus == 668) {
            this.checkPaper(topic.id, 2051)
        } else if (topic.topicStatus == 669) {
            this.checkPaper(topic.id, 2049)
        }
    }
    checkPaper = (id, stepId) => {
        const { actions } = this.props
        actions.createActivity(ADMIN_CHECK_PAPER, [{
            stepId: stepId,
            topicId: id
        }]).then(res => {
            const { data } = res.action.payload
            if (data.length == 0) {
                this.props.reloadData(() => {
                    notify.show(`Thầy/cô đã cập nhật đề tài thành công`, 'primary')
                })
            } else {
                notify.show(`Có lỗi xảy ra: ${data[0].error}`, 'danger')
            }
        }).catch(err => {
            notify.show(`Có lỗi xảy ra: ${err.response.data.message}`, 'danger')
        })
    }
    finish = (topicId, stepId) => {
        const val = confirm(`Thầy/cô chắc chắn đề tài bảo vệ không thành công?`)
        if (!val) return

        const { actions } = this.props
        actions.createActivity(ADMIN_FINISH_TOPIC, [{
            stepId,
            topicId
        }]).then(res => {
            const { data } = res.action.payload
            if (data.length == 0) {
                this.props.reloadData(() => {
                    notify.show(`Thầy/cô đã cập nhật đề tài thành công`, 'primary')
                })
            } else {
                notify.show(`Có lỗi xảy ra: ${data[0].error}`, 'danger')
            }
        }).catch(err => {
            notify.show(`Có lỗi xảy ra: ${err.response.data.message}`, 'danger')
        })
    }
    filterTopic = t => {
        const { filDepart, filLec, filStatus } = this.state
        if (filDepart && filDepart != t.departmentId) return false
        if (filStatus && filStatus != t.topicStatus) return false
        let supervisors = []
        if (t.mainSupervisorId) supervisors.push(t.mainSupervisorId)
        if (t.coSupervisorIds) supervisors.push(t.coSupervisorIds)
        if (filLec && !supervisors.find(s => s == filLec)) return false
        return true
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
    printTopics = () => {
        const { type, topics } = this.props
        const filteredTopic = topics.filter(this.filterTopic)
        let docData = []
        let count = 0
        topics.forEach(topic => {
            const tName = topic.isEnglish ? topic.englishTopicTitle : topic.vietnameseTopicTitle
            const mainSupervisor = this.getLecturerNameById(topic.mainSupervisorId)
            const coSupervisors = this.getCoSupervisorsNames(topic.coSupervisorIds)
            let docTopicItem = {
                index: count + 1,
                learnerCode: topic.learner.learnerCode,
                learnerName: topic.learner.fullname,
                topicName: tName,
                mainSupervisor,
                coSupervisors
            }
            count++
            docData.push(docTopicItem)
        })

        const docObj = {
            subtitle: `Danh sách đề tài đăng ký báo cáo tiến độ`,
            topics: docData
        }
        const templates = 'seminar'
        const documentNum = `Danh sách Seminar ${moment().format('L')}`
        generateDocx(`/templates/${templates}.docx`, docObj, `${documentNum}.docx`, null, null)
    }
    changeFilter = (e, filName) => {
        this.setState({
            [filName]: e.target.value
        })
        this.props.handlePageChange(1)
    }
    render() {
        const { filDepart, filLec, filStatus } = this.state
        const { activePage, topics, departments, lecturers, degrees, tabpanel, lowerStatus, higherStatus } = this.props
        if (!departments) return false
        if (!lecturers) return false
        if (topics.length == 0) return <div>
            Hiện đang không có đề tài.
        </div>
        const topicFiltered = topics.filter(this.filterTopic)
        const itemLength = topicFiltered.length
        return <div>
            <div class="row">
                <div class="pull-right form-inline">
                    <div class="form-group program-filter">
                        <label class="margin-right">Đơn vị</label>
                        <select class="form-control large-right" value={filDepart} onChange={e => this.changeFilter(e, 'filDepart')}>
                            <option value="">Tất cả</option>
                            { departments.map(d => <option key={d.id} value={d.id}>{d.name}</option>)}
                        </select>
                    </div>
                    <div class="form-group program-filter">
                        <label class="margin-right">Giảng viên</label>
                        <select class="form-control large-right" value={filLec} onChange={e => this.changeFilter(e, 'filLec')}>
                            <option value="">Tất cả</option>
                            { lecturers.map(l => {
                                const degree = degrees.find(d => d.id == l.degreeId )
                                    return <option key={l.id} value={l.id}>{degree && `${degree.name}.`} {l.fullname}</option>
                            }) }
                        </select>
                    </div>
                    <div class="form-group program-filter">
                        <label class="margin-right">Trạng thái</label>
                        <select class="form-control" value={filStatus} onChange={e => this.changeFilter(e, 'filStatus')}>
                            <option value="">Tất cả</option>
                            { arrayRange(lowerStatus, higherStatus).map(ts => <option key={ts} value={ts}>{TOPIC_STATUS[ts]}</option>) }}
                        </select>
                    </div>
                </div>
            </div>
            <div class="table-responsive">
                <table class="table table-hover table-condensed">
                    <thead>
                        <tr>
                            <th>TT</th>
                            <th>Mã HV</th>
                            <th>Tên học viên</th>
                            <th>Tên đề tài</th>
                            <th>GVHD chính</th>
                            <th>GV đồng HD</th>
                            <th>Trạng thái</th>
                            { tabpanel == 7 && <th class="text-center">Đã nộp quyển?</th> }
                            <th>Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        { topics && topics.length > 0 && topicFiltered.slice(ITEM_PER_PAGE * (activePage - 1), ITEM_PER_PAGE * activePage).map((topic, index) => {
                            const lecturer = lecturers.find(l => l.id == topic.mainSupervisorId)
                            const degree = lecturer && degrees.find(d => d.id == lecturer.degreeId)
                            const coSupervisors = this.getCoSupervisorsNames(topic.coSupervisorIds)

                            return (
                                <tr key={topic.id}>
                                    <td>{ITEM_PER_PAGE * (activePage-1) + index+1}</td>
                                    <td>{topic.learner.learnerCode}</td>
                                    <td>{topic.learner.fullname}</td>
                                    <td>{topic.vietnameseTopicTitle}</td>
                                    <td>{degree && `${degree.name}.`} {lecturer && lecturer.fullname}</td>
                                    <td>{coSupervisors}</td>
                                    <td>{TOPIC_STATUS[topic.topicStatus]}</td>
                                    { tabpanel == 7 && <td class="text-center">
                                        <input type="checkbox" checked={topic.topicStatus == 669 || topic.topicStatus == 670} onChange={e => this.changeCheckPaper(topic)} />
                                    </td> }
                                    <td>
                                        { topic.topicStatus == 700 && <div>
                                            <button class="btn btn-sm btn-primary btn-margin" onClick={e => this.finish(topic.id, 2080)}>Bảo vệ không thành công</button>
                                        </div>}
                                        <Link to={`/topic/${topic.id}`} class="btn btn-sm btn-primary btn-margin">Chi tiết</Link>
                                        { (topic.topicStatus >= 100 && topic.topicStatus <= 101) && <button class="btn btn-sm btn-primary btn-margin" onClick={this.deleteTopic(topic.id)}>Xóa</button> }
                                        { canCancelRequest(topic.topicStatus) && <button class="btn btn-sm btn-primary btn-margin" onClick={e => this.declineTopic(topic, getNextAdminDeclineStep(topic.topicStatus))}>Hủy</button> }
                                        { (topic.topicStatus == 888 || topic.topicStatus == 666) && <button class="btn btn-sm btn-primary btn-margin" onClick={e => this.declineTopic(topic, 2082)}>Quá hạn</button> }
                                        { (topic.topicStatus == 102 || topic.topicStatus == 891) && <button class="btn btn-sm btn-primary btn-margin" onClick={e => this.allowEditTopic(topic, 2100)}>Cho sửa đổi</button> }
                                    </td>
                                </tr>
                            )}) }
                    </tbody>
                </table>
            </div>
            <div class="text-center">
                <Pagination
                    activePage={parseInt(activePage)}
                    itemsCountPerPage={ITEM_PER_PAGE}
                    totalItemsCount={itemLength}
                    pageRangeDisplayed={PAGE_RANGE}
                    onChange={this.props.handlePageChange}
                />
            </div>
        </div>
    }
}

export default TopicsList
