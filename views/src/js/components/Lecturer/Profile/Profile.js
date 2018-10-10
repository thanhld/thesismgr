import React, { Component } from 'react'
import { notify, Loading } from 'Components'
import KnowledgeAreaModal from './KnowledgeAreaModal'

class LectureProfile extends Component {
    constructor(props) {
        super(props)
        this.state = {
            id: '',
            phone: '',
            website: '',
            address: '',
            otherEmail: '',
            description: '',
            editPhone: false,
            editWebsite: false,
            editAddress: false,
            editOtherEmail: false,
            editDescription: false
        }
    }

    componentWillMount() {
        const { actions, allAreas, degrees, departments, uid } = this.props
        if (!allAreas.isLoaded) actions.loadAreas()
        if (!degrees.isLoaded) actions.loadDegrees()
        if (!departments.isLoaded) actions.loadDepartments()
        this.reloadData()
    }

    componentWillReceiveProps(nextProps) {
        const { id, phone, website, address, otherEmail, description } = nextProps.profile.lecturer
        this.setState({
            id, phone, website, address, otherEmail, description
        })
    }

    reloadData = callback => {
        const { uid, actions } = this.props
        actions.loadLecturerAreas(uid)
        actions.loadLecturerInformation(uid).then(callback)
    }

    submitData = () => {
        const { actions } = this.props
        const { id, phone, website, address, otherEmail, description } = this.state
        const { editPhone, editOtherEmail } = this.state
        if (editPhone) {
            const phoneNumberRegex = /^[0-9 ]*$/
            if (!phoneNumberRegex.test(phone)) {
                notify.show('Số điện thoại không đúng. Vui lòng nhập lại.', 'danger')
                return false
            }
        }
        if (editOtherEmail) {
            const emailRegex = /^$|^\S+@\S+$/
            if (!emailRegex.test(otherEmail)) {
                notify.show('Địa chỉ email không đúng. Vui lòng nhập lại.', 'danger')
                return false
            }
        }
        actions.updateLecturerInformation({id, phone, website, address, otherEmail, description}).then(() => {
            this.reloadData(() => {
                this.setState({
                    editPhone: false,
                    editWebsite: false,
                    editAddress: false,
                    editOtherEmail: false,
                    editDescription: false
                })
                notify.show(`Đã cập nhật thông tin thầy/cô thành công`, 'primary')
            })
        }).catch(err => {
            notify.show(`Có lỗi xảy ra: ${err.response.data.message}`, 'danger')
        })
    }

    handleDescriptionChange = e => {
        this.setState({
            description: e.target.value
        })
    }

    handleSubmitDescription = () => {
        const { actions, uid } = this.props
        const { description } = this.state
        actions.updateLecturerInformation({
            id: uid,
            description
        }).then(resp => {
            this.reloadData(() => {
                this.setState({
                    editDescription: false
                })
                notify.show(`Đã cập nhật chủ đề nghiên cứu`, 'primary')
            })
        }).catch(err => {
            notify.show(`Có lỗi xảy ra: ${err.response.data.message}`, 'danger')
        })
    }

    deleteKnowledgeArea = area => e => {
        const val = confirm(`Thầy/cô có chắc chắn muốn xóa lĩnh vực ${area.name}?`)
        if (!val) return

        const { actions, uid } = this.props
        actions.deleteLecturerArea(uid, area.id).then(resp => {
            this.reloadData(() => {
                notify.show(`Đã xóa lĩnh vực quan tâm ${area.name}`, 'primary')
            })
        }).catch(err => {
            notify.show(`Có lỗi xảy ra: ${err.response.data.message}`, 'danger')
        })
    }

    handleUploadAvatar = () => {
        const { actions } = this.props;
        var files = document.getElementById('uploadAvatar').files
        var formData = new FormData()
        formData.append('uploadAvatar', files[0], files[0].name)
        actions.uploadAvatar(formData).then(response => {
            this.reloadData(() => {
                notify.show(`Thầy/cô đã cập nhật ảnh đại diện thành công`, 'primary')
            })
        }).catch(err => {
            notify.show(`Có lỗi xảy ra: ${err.response.data.message}`, 'danger')
        })
    }

    removeAvatar = (avatarUrl) => {
        const { actions } = this.props;
        actions.removeAvatar(avatarUrl).then(response => {
            this.reloadData(() => {
                notify.show(`Thầy/cô đã xóa ảnh đại diện thành công`, 'primary')
            })
        }).catch(err => {
            notify.show(`Có lỗi xảy ra: ${err.response.data.message}`, 'danger')
        })
    }

    render() {
        const { actions, profile, allAreas, degrees, departments, uid } = this.props
        const { isLoaded, lecturer, addedAreas } = profile
        const { phone, address, website, otherEmail, description } = this.state
        const { editPhone, editAddress, editWebsite, editOtherEmail, editDescription } = this.state
        const degree = degrees.list.find(d => d.id == lecturer.degreeId)
        const department = departments.list.find(d => d.id == lecturer.departmentId)

        if (!isLoaded) return <Loading />

        return <div class="profile-box">
            <br />
            <div class="row">
                <div class="officer-profile__avatar--mobile hidden-md hidden-lg col-xs-offset-3 col-xs-6 col-sm-offset-3 col-sm-6">
                    <img class="img-rounded img-responsive" src={ lecturer.avatarUrl ? lecturer.avatarUrl : "/images/brand-logo.jpg" } />
                    <div class="officer-avatar__editor-wrapper row">
                        <div class="officer-avatar__upload col-xs-offset-1 col-xs-5 col-sm-offset-1 col-sm-5 col-md-offset-1 col-md-5">
                            <label for="uploadAvatar" class="clickable">
                                <i class="fa fa-camera" aria-hidden="true"></i>
                                <span>Cập nhật ảnh</span>
                            </label>
                            <input type="file" accept="image/png,image/jpeg" name="uploadAvatar" id="uploadAvatar"
                                onChange={() => this.handleUploadAvatar()}/>
                        </div>

                        { lecturer.avatarUrl && lecturer.avatarUrl.replace(/\s+/g, '') != '' &&
                        <div class="officer-avatar__remove col-xs-5 col-sm-5 col-md-5 clickable"
                            onClick = {() => this.removeAvatar( lecturer.avatarUrl )}>
                            <i class="fa fa-times text-danger" aria-hidden="true"></i>
                            <span>Xóa ảnh</span>
                        </div> }
                    </div>
                </div>

                <div class="col-xs-offset-1 col-xs-11 col-sm-offset-1 col-sm-11 col-md-offset-1 col-md-6">
                    <div class="profile-info-1">
                        <i class="fa fa-user fa-fw"></i>
                        {degree && `${degree.name}.`} {lecturer.fullname}
                    </div>
                    <div class="profile-info-2">
                        <i class="fa fa-address-card fa-fw"></i>
                        Mã cán bộ: <span>{lecturer.officerCode}</span>
                    </div>
                    <div class="profile-info-2">
                        <i class="fa fa-graduation-cap fa-fw"></i>
                        Chức vụ: <span>Giảng viên</span>
                    </div>
                    <div class="profile-info-2">
                        <i class="fa fa-object-group fa-fw"></i>
                        Bộ môn: <span>{department && department.name}</span>
                    </div>
                    <div class="profile-info-2">
                        <i class="fa fa-newspaper-o fa-fw"></i>
                        Học hàm, học vị: <span>{degree && degree.name}</span>
                    </div>
                    <div class="profile-info-2">
                        <i class="fa fa-mobile-phone fa-fw"></i>
                        Số điện thoại: {!editPhone && <span class="margin-right">{lecturer.phone || <span>
                            <i>Chưa có</i>
                        </span>}</span>}
                        {!editPhone &&
                            <i class="fa fa-edit" title="Nhấn để sửa số điện thoại" onClick={e => this.setState({editPhone: true})}></i>}
                        { editPhone &&
                            <span>
                                <input type="text" class="input-lecture-profile" size="50" value={phone || ''} onChange={e => this.setState({phone: e.target.value})} />
                                {" "}
                                <i class="fa fa-check text-success" title="Nhấn để lưu" onClick={this.submitData}></i>
                                <i class="fa fa-times text-danger" title="Nhấn để bỏ qua" onClick={e => this.setState({editPhone: false, phone: lecturer.phone})}></i>
                            </span> }
                    </div>
                    <div class="profile-info-2">
                        <i class="fa fa-envelope fa-fw"></i>
                        VNU email: <span>{lecturer.vnuMail}</span>
                    </div>
                    <div class="profile-info-2">
                        <i class="fa fa-envelope-o fa-fw"></i>
                        Email khác: {!editOtherEmail && <span class="margin-right">{lecturer.otherEmail || <span>
                            <i>Chưa có</i>
                        </span>}</span>}
                        {!editOtherEmail &&
                            <i class="fa fa-edit" title="Nhấn để sửa email khác" onClick={e => this.setState({editOtherEmail: true})}></i>}
                        { editOtherEmail &&
                            <span>
                                <input type="text" class="input-lecture-profile" size="50" value={otherEmail || ''} onChange={e => this.setState({otherEmail: e.target.value})} />
                                {" "}
                                <i class="fa fa-check text-success" title="Nhấn để lưu" onClick={this.submitData}></i>
                                <i class="fa fa-times text-danger" title="Nhấn để bỏ qua" onClick={e => this.setState({editOtherEmail: false, otherEmail: lecturer.otherEmail})}></i>
                            </span> }
                    </div>
                    <div class="profile-info-2">
                        <i class="fa fa-paper-plane fa-fw"></i>
                        Website: {!editWebsite && <span class="margin-right">{lecturer.website || <span>
                            <i>Chưa có</i>
                        </span>}</span>}
                        {!editWebsite &&
                            <i class="fa fa-edit" title="Nhấn để sửa địa chỉ web" onClick={e => this.setState({editWebsite: true})}></i>}
                        { editWebsite &&
                            <span>
                                <input type="text" class="input-lecture-profile" size="50" value={website || ''} onChange={e => this.setState({website: e.target.value})} />
                                {" "}
                                <i class="fa fa-check text-success" title="Nhấn để lưu" onClick={this.submitData}></i>
                                <i class="fa fa-times text-danger" title="Nhấn để bỏ qua" onClick={e => this.setState({editWebsite: false, website: lecturer.website})}></i>
                            </span> }
                    </div>

                    <div class="profile-info-2">
                        <i class="fa fa-university fa-fw"></i>
                        Địa chỉ: {!editAddress && <span class="margin-right">{lecturer.address || <span>
                            <i>Chưa có</i>
                        </span>}</span>}
                        {!editAddress &&
                            <i class="fa fa-edit" title="Nhấn để sửa địa chỉ" onClick={e => this.setState({editAddress: true})}></i>}
                        { editAddress &&
                            <span>
                                <input type="text" class="input-lecture-profile" size="50" value={address || ''} onChange={e => this.setState({address: e.target.value})} />
                                {" "}
                                <i class="fa fa-check text-success" title="Nhấn để lưu" onClick={this.submitData}></i>
                                <i class="fa fa-times text-danger" title="Nhấn để bỏ qua" onClick={e => this.setState({editAddress: false, address: lecturer.address})}></i>
                            </span> }
                    </div>
                </div>

                <div class="officer-profile__avatar--desktop hidden-xs hidden-sm col-md-4">
                    <img class="img-rounded img-responsive" src={ lecturer.avatarUrl ? lecturer.avatarUrl : "/images/brand-logo.jpg" } />
                    <div class="officer-avatar__editor-wrapper row">
                        <div class="officer-avatar__upload col-xs-offset-1 col-xs-5 col-sm-offset-1 col-sm-5 col-md-offset-1 col-md-5">

                            <label for="uploadAvatar" class="clickable">
                                <i class="fa fa-camera" aria-hidden="true"></i>
                                <span>Cập nhật ảnh</span>
                            </label>
                            <input type="file" accept="image/png,image/jpeg" name="uploadAvatar" id="uploadAvatar"
                                onChange={() => this.handleUploadAvatar() }/>
                        </div>

                        { lecturer.avatarUrl && lecturer.avatarUrl.replace(/\s+/g, '') != '' &&
                        <div class="officer-avatar__remove col-xs-5 col-sm-5 col-md-5 clickable"
                            onClick = {() => this.removeAvatar( lecturer.avatarUrl )}>
                            <i class="fa fa-times text-danger" aria-hidden="true"></i>
                            <span>Xóa ảnh</span>
                        </div> }
                    </div>
                </div>
            </div>
            <br />
            <div class="row">
                <div class="col-xs-offset-1 col-xs-9">
                    <div class="row">
                        <div class="col-xs-8 profile-info-1">Chủ đề nghiên cứu</div>
                        <div class="col-xs-4 profile-info-1">
                            <div class="pull-right">
                                { !editDescription && <button type="button" class="btn btn-success btn-margin btn-sm" onClick={() => this.setState({editDescription: true})}>
                                    Chỉnh sửa
                                </button> }
                                { editDescription && <div>
                                    <button type="button" class="btn btn-primary btn-margin btn-sm" onClick={this.handleSubmitDescription}>
                                        Cập nhật
                                    </button>
                                    <button type="button" class="btn btn-default btn-margin btn-sm" onClick={() => this.setState({editDescription: false, description: lecturer.description})}>
                                        Bỏ qua
                                    </button>
                                </div> }
                            </div>
                        </div>
                    </div>
                    <div class="research-content profile-info-2">
                        { !editDescription && lecturer && (lecturer.description || `Thầy/cô chưa thêm chủ đề nghiên cứu. Chọn "Chỉnh sửa" để thêm chủ đề nghiên cứu.`) }
                        { editDescription && <textarea class="form-control profile-info-2" rows="8" value={description} onChange={this.handleDescriptionChange} /> }
                    </div>
                    <div class="knowledge-area">
                        <div class="row">
                            <div class="col-xs-8 profile-info-1">Lĩnh vực quan tâm</div>
                            <div class="col-xs-4 profile-info-1">
                                <div class="pull-right">
                                    <button type="button" class="btn btn-success btn-margin btn-sm" data-toggle="modal" data-target="#modalAddResearchFeild">
                                        Thêm mới
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="knowledge-content profile-info-2">
                            { addedAreas.length > 0 ? <ul>
                                { addedAreas.map(a => <li key={a.id}>
                                    <div class="knowledge-name">{a.name}</div>
                                    <span class="knowledge-delete text-danger clickable" onClick={this.deleteKnowledgeArea(a)}>
                                        <i class="fa fa-window-close" aria-hidden="true" title={`Xóa lĩnh vực ${a.name}`}></i>
                                    </span>
                                </li>) }
                            </ul>: `Thầy/cô chưa quan tâm lĩnh vực nào. Chọn "Thêm mới" để thêm lĩnh vực quan tâm.`}
                        </div>
                    </div>
                </div>
            </div>
            <KnowledgeAreaModal
                lecturerId={uid}
                actions={actions}
                reloadData={this.reloadData}
                addedAreas={addedAreas}
                allAreas={allAreas.areas} />
            <br />
        </div>
    }
}

export default LectureProfile
