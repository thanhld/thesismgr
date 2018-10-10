import React, { Component } from 'react'
import { Loading, notify } from 'Components'
import {
    LEARNER_CREATE_TOPIC,
    LEARNER_CHANGE_TOPIC,
    MIN_QUOTA,
    OFFICER_LECTURER_ROLE
} from 'Constants'
import { mailerActions } from 'Actions';

class RegisterTopic extends Component {
    constructor(props) {
        super(props)
        const { topics } = props
        this.state = {
            vietnameseTopicTitle: topics.vietnameseTopicTitle || '',
            englishTopicTitle: topics.englishTopicTitle || '',
            isEnglish: topics.isEnglish || false,
            description: topics.description || '',
            tags: topics.tags || '',
            mainSupervisorId: topics.mainSupervisorId || '',
            coSupervisorIds: topics.coSupervisorIds || '',
            newMain: {
                name: '',
                degree: '',
                department: ''
            },
            newCo: {
                name: '',
                degree: '',
                department: ''
            },
            error: false,
            message: ''
        }
    }
    checkLecturers = () => {
        const { lecturers } = this.props
        const { mainSupervisorId, coSupervisorIds } = this.state
        const idList = []
        if (mainSupervisorId && mainSupervisorId != 'newMain') idList.push(mainSupervisorId)
        if (coSupervisorIds && coSupervisorIds != 'newCo') idList.push(coSupervisorIds)
        let check = false
        idList.forEach(id => {
            const tmp = lecturers.list.find(l => l.id == id)
            if (tmp.role != 5) check = true
        })
        return check
    }
    getRequestedSupervisorId = () => {
        const { mainSupervisorId, coSupervisorIds } = this.state
        if (mainSupervisorId != 'newMain') return mainSupervisorId
        if (coSupervisorIds != 'newCo') return coSupervisorIds
    }
    calculateNumLecturer = () => {
        let result = 0
        const { mainSupervisorId, coSupervisorIds } = this.state
        if (mainSupervisorId) result++
        if (coSupervisorIds) result++
        return result
    }
    checkQuota = numLecturer => {
        const { mainSupervisorId, coSupervisorIds} = this.state
        const { degrees, lecturers, topics, quotas } = this.props
        const { topicType } = topics
        const lecField = topicType == 1 ? 'numberOfStudent' : topicType == 2 ? 'numberOfGraduated' : 'numberOfResearcher'
        const quotaField = topicType == 1 ? 'maxStudent' : topicType == 2 ? 'maxGraduated' : 'maxResearcher'
        if (mainSupervisorId && mainSupervisorId != 'newMain') {
            const factor = topicType == 1 ? 'mainFactorStudent' : topicType ? 'mainFactorGraduated' : 'mainFactorResearcher'
            const lecturer = lecturers.list.find(l => l.id == mainSupervisorId)
            if (lecturer && lecturer.role == OFFICER_LECTURER_ROLE) {
                const lecQuota = quotas.list.find(q => q.degreeId == lecturer.degreeId)
                if (lecQuota[quotaField] - lecturer[lecField] < 1.0 * lecQuota[factor] / numLecturer) {
                    return lecturer.fullname
                }
            }
        }
        if (coSupervisorIds && coSupervisorIds != 'newCo') {
            const factor = topicType == 1 ? 'coFactorStudent' : topicType ? 'coFactorGraduated' : 'coFactorResearcher'
            const lecturer = lecturers.list.find(l => l.id == coSupervisorIds)
            if (lecturer && lecturer.role == OFFICER_LECTURER_ROLE) {
                const lecQuota = quotas.list.find(q => q.degreeId == lecturer.degreeId)
                if (lecQuota[quotaField] - lecturer[lecField] < 1.0 * lecQuota[factor] / numLecturer) {
                    return lecturer.fullname
                }
            }
        }
        return 'OK'
    }
    isUniqueLecturers = () => {
        const { mainSupervisorId, coSupervisorIds } = this.state
        if (mainSupervisorId && mainSupervisorId == coSupervisorIds) return false
        if (coSupervisorIds && coSupervisorIds == mainSupervisorId) return false
        return true
    }
    submitFile = callback => {
        const { actions } = this.props;
        let files = document.getElementById('uploadRegisterAttm').files
        let formData = new FormData()
        formData.append('uploadRegisterAttm', files[0], files[0].name)
        actions.uploadAttachment(formData).then(response => {
            callback(response.action.payload.data.url)
        }).catch(err => {
            this.setState({
                error: true,
                message: err.response.data.message
            })
        })
    }
    submitTopic = registerUrl => {
        const { action, actions } = this.props
        const { vietnameseTopicTitle, englishTopicTitle, isEnglish, description, tags, mainSupervisorId, coSupervisorIds } = this.state
        const { newMain, newCo } = this.state
        let outOfficers = []
        if (mainSupervisorId == 'newMain') outOfficers.push({
            fullname: newMain.name.trim(),
            degreeId: newMain.degree,
            departmentName: newMain.department.trim()
        })
        if (coSupervisorIds == 'newCo') outOfficers.push({
            fullname: newCo.name.trim(),
            degreeId: newCo.degree,
            departmentName: newCo.department.trim()
        })
        const createTopic = outOfficerIds => {
            let data = {
                vietnameseTopicTitle: vietnameseTopicTitle.trim(),
                englishTopicTitle: englishTopicTitle.trim(),
                isEnglish,
                description: description.trim(),
                tags: tags.trim()
            }
            if (registerUrl) data['registerUrl'] = registerUrl
            let coFinal = ''
            if (coSupervisorIds && coSupervisorIds != 'newCo') {
                coFinal = coSupervisorIds
            } else if (!coSupervisorIds && action == 'update') {
                let tmp = this.props.topics && this.props.topics.coSupervisorIds
                if (tmp) coFinal = 'del'
            }
            if (coFinal) data['coSupervisorIds'] = coFinal
            else data['coSupervisorIds'] = null
            if (outOfficerIds) data['outOfficerIds'] = outOfficerIds
            else data['outOfficerIds'] = null
            if (mainSupervisorId != 'newMain') data['mainSupervisorId'] = mainSupervisorId
            else data['mainSupervisorId'] = null
            if (action == 'update') {
                Object.keys(data).forEach(key => {
                    if (data[key] == this.props.topics[key]) delete data[key]
                })
            }
            actions.createActivity(this.props.action == 'create' ? LEARNER_CREATE_TOPIC : LEARNER_CHANGE_TOPIC, [{
                stepId: action == 'create' ? 100 : 101,
                topicId: this.props.topics.id,
                requestedSupervisorId: this.getRequestedSupervisorId(),
                data
            }]).then(res => {
                const { data } = res.action.payload
                if (data.length > 0) {
                    notify.show(`Có lỗi xảy ra: ${data[0].error}`, 'danger')
                } else {
                    mailerActions.approveTopicMail();
                    this.props.reloadTopic(() => {
                        if (this.props.action == 'create') {
                            notify.show('Bạn đã đăng ký đề tài thành công', 'primary')
                        } else {
                            notify.show('Bạn đã yêu cầu chỉnh sửa thành công', 'primary')
                            this.props.finishRequest()
                        }
                    })
                }
            }).catch(err => {
                notify.show(`Có lỗi xảy ra: ${err.response.data.message}`, 'danger')
            })
        }
        if (outOfficers.length == 0) createTopic()
        else {
            actions.createOutOfficers(outOfficers).then(res => {
                const { data } = res.action.payload
                let outOfficerIds = ['', '']
                let checkOut = [false, false]
                if (mainSupervisorId == 'newMain') checkOut[0] = true
                if (coSupervisorIds == 'newCo') checkOut[1] = true
                let cancel = false
                let now = 0
                data.forEach((d, index) => {
                    while (!checkOut[now]) now++
                    const { id, error } = d
                    if (error) {
                        cancel = true
                        notify.show(`Có lỗi xảy ra: ${error}`, 'danger')
                    } else {
                        outOfficerIds[now] = id
                        now++
                    }
                })
                if (!cancel) createTopic(outOfficerIds.join())
            }).catch(err => {
                notify.show(`Có lỗi xảy ra: ${err.response.data.message}`, 'danger')
            })
        }
    }
    handleSubmit = e => {
        e.preventDefault()
        if (!this.checkLecturers()) {
            this.setState({
                error: true,
                message: 'Bạn phải chọn ít nhất một giảng viên hướng dẫn trong trường.'
            })
            return
        } else {
            this.setState({
                error: false
            })
        }
        if (!this.isUniqueLecturers()) {
            this.setState({
                error: true,
                message: 'Bạn cần phải chọn các giảng viên hướng dẫn khác nhau.'
            })
            return false
        } else {
            this.setState({
                error: false
            })
        }
        const { action } = this.props
        if (action == 'create') {
            const numLecturer = this.calculateNumLecturer()
            const checkQuota = this.checkQuota(numLecturer)
            if (checkQuota != 'OK') {
                this.setState({
                    error: true,
                    message: `Bạn không thể đăng ký vì giảng viên ${checkQuota} sẽ quá lượng định mức.\n\t\tVui lòng chọn giảng viên hướng dẫn khác, hoặc tăng số lượng giảng viên hướng dẫn.`
                })
                return
            } else {
                this.setState({
                    error: false
                })
            }
        }
        const { topicType } = this.props.topics
        if (topicType == 2 && action == 'create') {
            const files = document.getElementById('uploadRegisterAttm').files
            if (files.length == 0) {
                this.setState({
                    error: true,
                    message: 'Vui lòng tải tệp đề cương'
                })
                return false
            } else {
                this.setState({
                    error: false
                })
            }
        }
        const confirmText = action == 'create' ? 'Bạn có chắc chắn đăng ký đề tài này?\nBạn chỉ có thể điều chỉnh đề tài khi Khoa mở đợt điều chỉnh.' : 'Bạn có chắc chắn chỉnh sửa đề tài này?\nBạn có thể hủy chỉnh sửa trước khi Khoa phê duyệt chỉnh sửa của bạn.'
        const val = confirm(confirmText)
        if (val) {
            const haveFile = (document.getElementById('uploadRegisterAttm') && document.getElementById('uploadRegisterAttm').files && document.getElementById('uploadRegisterAttm').files.length > 0)
            if (topicType == 2 && haveFile) {
                this.submitFile(this.submitTopic)
            } else {
                this.submitTopic()
            }
        }
    }
    handleRefresh = () => {
        const { topics } = this.props
        this.setState({
            vietnameseTopicTitle: topics.vietnameseTopicTitle || '',
            englishTopicTitle: topics.englishTopicTitle || '',
            isEnglish: topics.isEnglish || false,
            description: topics.description || '',
            tags: topics.tags || '',
            mainSupervisorId: topics.mainSupervisorId || '',
            coSupervisorIds: topics.coSupervisorIds || '',
            error: false
        })
    }
    render() {
        const { lecturers, departments, degrees, quotas, action } = this.props
        if (!lecturers.isLoaded) return <Loading />
        if (!degrees.isLoaded) return <Loading />
        if (!departments.isLoaded) return <Loading />
        if (!quotas.isLoaded) return <Loading />
        const { vietnameseTopicTitle, englishTopicTitle, isEnglish, description, tags, mainSupervisorId, coSupervisorIds } = this.state
        const { newMain, newCo, error, message } = this.state
        const { topicType } = this.props.topics
        const lecField = topicType == 1 ? 'numberOfStudent' : topicType == 2 ? 'numberOfGraduated' : 'numberOfResearcher'
        const quotaField = topicType == 1 ? 'maxStudent' : topicType == 2 ? 'maxGraduated' : 'maxResearcher'
        const optLecturer = <optgroup label="Giảng viên trong trường">
            {lecturers && lecturers.list.filter(l => l.role == 3 || l.role == 6).map(l => {
                const degree = degrees.list.find(d => d.id == l.degreeId)
                const lecQuota = quotas.list.find(q => q.degreeId == l.degreeId)
                const overQuota = lecQuota[quotaField] - l[lecField] < MIN_QUOTA
                return (
                    <option key={l.id} value={l.id} disabled={overQuota}>{degree && `${degree.name}.`} {l.fullname} {overQuota && action == 'create' && "- quá định mức"}</option>
                )})}
        </optgroup>
        const optOutLecturer = <optgroup label="Giảng viên đơn vị ngoài">
            {lecturers && lecturers.list.filter(l => l.role == 5).map(l => {
                const degree = degrees.list.find(d => d.id == l.degreeId)
                const department = departments.list.find(d => d.id == l.departmentId)
                const lecQuota = quotas.list.find(q => q.degreeId == l.degreeId)
                const overQuota = lecQuota[quotaField] - l[lecField] < MIN_QUOTA
                return (
                    <option key={l.id} value={l.id}>{degree && `${degree.name}.`} {l.fullname} {department && `- ${department.name}`} {overQuota && "- quá định mức"}</option>
                )})}
        </optgroup>
        const optionDegrees = degrees && degrees.list.map(d => <option key={d.id} value={d.id}>{d.name}</option>)
            return <div>
                <form class="form-horizontal register-topic-form" onSubmit={this.handleSubmit}>
                    <div class="row">
                        <div class="col-xs-12 page-title">{action == "create" ? 'Đăng ký đề tài' : 'Chỉnh sửa đề tài'}</div>
                    </div>
                    <div class="register-form-content">
                        <div class="form-group">
                            <label class="col-sm-3 control-label">Tên đề tài (Tiếng Việt)</label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" value={vietnameseTopicTitle} onChange={e => this.setState({vietnameseTopicTitle: e.target.value})} required />
                            </div>
                        </div>
                        { isEnglish && <div class="form-group">
                            <label class="col-sm-3 control-label">Tên đề tài (Tiếng Anh)</label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" value={englishTopicTitle} onChange={e => this.setState({englishTopicTitle: e.target.value})} required />
                            </div>
                        </div> }
                        <div class="form-group">
                            <label class="col-sm-3 control-label">Thực hiện tiếng Anh? </label>
                            <div class="col-sm-8">
                                <input type="checkbox" checked={isEnglish} onChange={e => this.setState({isEnglish: e.target.checked})} />
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-3 control-label">Giới thiệu chung</label>
                            <div class="col-sm-8">
                                <textarea rows={4} class="form-control" value={description} onChange={e => this.setState({description: e.target.value})} required />
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-3 control-label">Tags</label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" value={tags} placeholder="Ví dụ: Tin sinh học, Học máy... (Có thể bỏ trắng)" onChange={e => this.setState({tags: e.target.value})} />
                            </div>
                        </div>
                        { topicType == 2 && <div class="form-group">
                            <label class="col-sm-3 control-label">Đề cương</label>
                            <div class="col-sm-8">
                                <input id="uploadRegisterAttm" type="file" class="form-control" accept="application/pdf" />
                                { action == 'update' && <i class="fa fa-exclamation-circle" aria-hidden="true"> Tải tệp mới nếu muốn thay đổi đề cương</i> }
                                { action == 'update' && <br /> }
                                <i class="fa fa-file-pdf-o" aria-hidden="true"> Chỉ chấp nhận định dạng PDF</i>
                            </div>
                        </div> }
                        <div class="form-group">
                            <label class="col-sm-3 control-label">Giảng viên hướng dẫn chính</label>
                            <div class="col-sm-8">
                                <select class="form-control" value={mainSupervisorId} onChange={e => this.setState({mainSupervisorId: e.target.value})} required>
                                    <option value="" hidden disabled>Chọn giảng viên hướng dẫn chính</option>
                                    {optLecturer}
                                    {optOutLecturer}
                                    <optgroup label="Giảng viên chưa có trong danh sách">
                                        <option value="newMain">Nhập thông tin giảng viên</option>
                                    </optgroup>
                                </select>
                            </div>
                        </div>
                        { mainSupervisorId == 'newMain' && <div>
                            <div class="form-group">
                                <label class="col-sm-offset-3 col-sm-2">Tên giảng viên</label>
                                <div class="col-sm-6">
                                    <input class="form-control" value={newMain.name} onChange={e => {this.setState({newMain: {...newMain, name: e.target.value}})}} required/>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-offset-3 col-sm-2">Học hàm, học vị</label>
                                <div class="col-sm-6">
                                    <select class="form-control" value={newMain.degree} onChange={e => {this.setState({newMain: {...newMain, degree: e.target.value}})}} required>
                                        <option value="" hidden disabled>Chọn học hàm, học vị</option>
                                        { optionDegrees }
                                    </select>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-offset-3 col-sm-2">Đơn vị công tác</label>
                                <div class="col-sm-6">
                                    <input class="form-control" value={newMain.department} onChange={e => {this.setState({newMain: {...newMain, department: e.target.value}})}} required/>
                                </div>
                            </div>
                        </div> }
                        <div class="form-group">
                            <label class="col-sm-3 control-label">Giảng viên đồng hướng dẫn</label>
                            <div class="col-sm-8">
                                <select class="form-control" value={coSupervisorIds} onChange={e => this.setState({coSupervisorIds: e.target.value})}>
                                    <option value="">Không có</option>
                                    {optLecturer}
                                    {optOutLecturer}
                                    <optgroup label="Giảng viên chưa có trong danh sách">
                                        <option value="newCo">Nhập thông tin giảng viên</option>
                                    </optgroup>
                                </select>
                            </div>
                        </div>
                        { coSupervisorIds == 'newCo' && <div>
                            <div class="form-group">
                                <label class="col-sm-offset-3 col-sm-2">Tên giảng viên</label>
                                <div class="col-sm-6">
                                    <input class="form-control" value={newCo.name} onChange={e => {this.setState({newCo: {...newCo, name: e.target.value}})}} required/>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-offset-3 col-sm-2">Học hàm, học vị</label>
                                <div class="col-sm-6">
                                    <select class="form-control" value={newCo.degree} onChange={e => {this.setState({newCo: {...newCo, degree: e.target.value}})}} required>
                                        <option value="" hidden={action == 'create'} disabled={action == 'create'}>Chọn học hàm, học vị</option>
                                        { optionDegrees }
                                    </select>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-offset-3 col-sm-2">Đơn vị công tác</label>
                                <div class="col-sm-6">
                                    <input class="form-control" value={newCo.department} onChange={e => {this.setState({newCo: {...newCo, department: e.target.value}})}} required/>
                                </div>
                            </div>
                        </div> }

                        { error && <div class="alert alert-danger wrap-text">
                            Có lỗi xảy ra: {message}
                        </div> }
                        <div class="col-sm-11">
                            <div class="pull-right">
                                <button type="submit" class="btn btn-primary btn-sm margin-right">{action == 'create' ? 'Đăng ký' : 'Chấp nhận'}</button>
                                <button type="button" class="btn btn-default btn-sm margin-right" onClick={this.handleRefresh}>Làm mới</button>
                                { action == 'update' && <button type="button" class="btn btn-default btn-sm btn-margin" onClick={this.props.finishRequest}>Quay lại</button> }
                            </div>
                        </div>
                    </div>
                </form>
            </div>
    }
}

export default RegisterTopic
