import React, { Component } from 'react'
import { Link } from 'react-router'
import { Loading } from 'Components'

class PublicLecturer extends Component {

    constructor(props) {
        super(props)
    }

    componentWillMount() {
        const { actions, params, degrees, departments } = this.props
        if (!degrees.isLoaded) actions.loadDegrees()
        if (!departments.isLoaded) actions.loadDepartments()
        actions.loadPublicLecturer(params.id)
        actions.loadPublicLecturerAreas(params.id)
    }

    render() {
        const { lecturer, areas, degrees, departments } = this.props
        if (!degrees.isLoaded) return <Loading />
        if (!departments.isLoaded) return <Loading />
        const degree = degrees.list.find(d => d.id == lecturer.degreeId)
        const department = departments.list.find(d => d.id == lecturer.departmentId)
        return <div><div class="lecturer-public-info profile-box">
            <div class="row general-info">
                <div class="hidden-md hidden-lg col-xs-offset-3 col-xs-7 col-sm-offset-3 col-sm-7">
                    <div class="avatar-box--mobile">
                        <img class="img-thumbnail img-responsive" src={ lecturer.avatarUrl ? lecturer.avatarUrl : "/images/brand-logo.jpg" }/>
                    </div>
                </div>
                <div class="col-xs-12 col-sm-12 col-md-7">
                    <div class="profile-info-1">
                        {degree && `${degree.name}.`} {lecturer.fullname}
                    </div>
                    <div class="profile-info-2">
                        <i class="fa fa-object-group fa-fw"></i>
                        <span>Đơn vị:</span> {department && department.name || 'Chưa có'}
                    </div>
                    <div class="profile-info-2">
                        <i class="fa fa-graduation-cap fa-fw"></i>
                        <span>Học hàm, học vị:</span> {degree && degree.name || 'Chưa có'}
                    </div>
                    <div class="profile-info-2">
                        <i class="fa fa-envelope fa-fw"></i>
                        <span>VNU email:</span> {lecturer.vnuMail || 'Chưa có'}
                    </div>
                    <div class="profile-info-2">
                        <i class="fa fa-envelope-o fa-fw"></i>
                        <span>Email khác:</span> {lecturer.otherEmail || 'Chưa có'}
                    </div>
                    <div class="profile-info-2">
                        <i class="fa fa-paper-plane fa-fw"></i>
                        <span>Website:</span> {lecturer.website || 'Chưa có'}
                    </div>
                </div>
                <div class="hidden-xs hidden-sm col-md-4">
                    <div class="avatar-box--desktop">
                        <img class="img-thumbnail img-responsive" src={ lecturer.avatarUrl ? lecturer.avatarUrl : "/images/brand-logo.jpg" }/>
                    </div>
                </div>
            </div>
            <div class="row technique-info">
                <div class="col-sm-12 col-md-12">
                    <div class="profile-info-1">
                        Chủ đề nghiên cứu
                    </div>
                    <div class="profile-info-2 wrap-text">
                        {lecturer.description || 'Hiện tại giảng viên chưa cập nhật chủ đề nghiên cứu.'}
                    </div>
                </div>
                <div class="col-sm-12 col-md-12">
                    <div class="profile-info-1">
                        Lĩnh vực quan tâm
                    </div>
                    { areas.length == 0 ? <div>
                        Hiện giảng viên chưa quan tâm lĩnh vực nào.
                    </div> : <ul>
                        { areas.map(a => <li key={a.id} class="profile-info-3">{a.name}</li>)}
                    </ul> }
                </div>
            </div>
        </div>
        <div class="back-to-browse-lecturer__link">
            <Link to="/browse-lecturers">Quay lại trang Tìm kiếm giảng viên</Link>
        </div>
    </div>
    }
}

export default PublicLecturer
