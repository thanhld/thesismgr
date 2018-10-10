import React, { Component } from 'react'
import { Tab, Tabs, TabList, TabPanel } from 'react-tabs'
import { browserHistory } from 'react-router'
import { Loading } from 'Components'
import BrowseLecturersArea from './BrowseLecturersArea'

class BrowseLecturers extends Component {
    constructor() {
        super()
        this.state = {
            departmentFilter: localStorage.getItem('browse-department') || "",
            areaFilter: localStorage.getItem('browse-area') || "",
            isLoadingLecturers: false
        }
        this.loadLecturesHasArea = this.loadLecturesHasArea.bind(this)
    }

    componentWillMount() {
        const { actions, departments, degrees, areas } = this.props
        if (!departments.isLoaded) actions.loadDepartments()
        if (!areas.isLoaded) actions.loadAreas()
        if (!degrees.isLoaded) actions.loadDegrees()
    }

    loadLecturers = department => e => {
        e.preventDefault()
        const { actions } = this.props
        localStorage.setItem('browse-department', department.id)
        localStorage.setItem('browse-area', '')
        this.setState({
            departmentFilter: department.id,
            areaFilter: '',
            isLoadingLecturers: true
        })
        actions.loadLecturersOfDepartment(department.id).then(() => {
            this.setState({
                isLoadingLecturers: false
            })
        })
    }

    loadLecturesHasArea(area, e) {
        e.preventDefault();
        const { actions } = this.props
        localStorage.setItem('browse-area', area.id)
        localStorage.setItem('browse-department', '')
        this.setState({
            departmentFilter: '',
            areaFilter: area.id,
            isLoadingLecturers: true
        })
        actions.loadLecturersHasArea(area.id).then(() => {
            this.setState({
                isLoadingLecturers: false
            })
        })
    }

    handleClickLecturer = id => {
        browserHistory.push(`/browse-lecturer/${id}`)
    }

    handleChangeTab = (idx, lastIdx, e) => {
        localStorage.setItem('browse-tab', idx)
    }

    render() {
        const { departmentFilter, areaFilter, isLoadingLecturers, tabSelected } = this.state
        const { userFaculty, degrees, departments, areas, lecturers } = this.props
        if (!departments.isLoaded) return <Loading />
        if (!degrees.isLoaded) return <Loading />
        if (!areas.isLoaded) return <Loading />
        const areaFilterObj = areas.areas.find(d => d.id == areaFilter)
        const departmentFilterObj = departments.list.find(d => d.id == departmentFilter)
        return (
            <div>
                <div class="row">
                    <div class="col-xs-12">
                        <span class="page-title">Tìm kiếm giảng viên</span>
                        <span class="browse-guide"><i class="browse-guide-highlight"> Hướng dẫn:</i> Chọn đơn vị hoặc lĩnh vực, tiếp đó chọn giảng viên để xem chi tiết.</span>
                    </div>
                </div>
                <br />
                <div class="row">
                    <div class="col-xs-12 col-sm-5">
                        <Tabs
                            selectedIndex={Number(localStorage.getItem('browse-tab')) || 0}
                            onSelect={this.handleChangeTab}>
                            <TabList>
                                <Tab>Đơn vị công tác</Tab>
                                <Tab>Lĩnh vực quan tâm</Tab>
                            </TabList>
                            <TabPanel>
                                <div class="box-list">
                                    { departments.list.map(obj => {
                                        if (obj.type == 4)
                                            return false
                                        if (obj.type == 3 && userFaculty != obj.facultyId)
                                            return false
                                        return (
                                            <div key={obj.id}>
                                                <a class={`clickable department-item ${ departmentFilter == obj.id && "selected"}`} onClick={this.loadLecturers(obj)}>{obj.name} {obj.type != 3 && userFaculty != obj.facultyId && `-  ${obj.facultyName}`}</a>
                                            </div>
                                        )}) }
                                </div>
                            </TabPanel>
                            <TabPanel>
                                <div class="knowledge-area-box">
                                    <BrowseLecturersArea
                                        areas={areas.areas}
                                        areaFilter={areaFilter}
                                        loadLecturersHasArea={this.loadLecturesHasArea}
                                    />
                                </div>
                            </TabPanel>
                        </Tabs>
                    </div>
                    <div class="col-xs-12 col-sm-7">
                        <div class="browse-content">
                            <div class="browse-title">
                                { departmentFilter !== '' ? `Danh sách giảng viên thuộc ${departmentFilterObj && departmentFilterObj.name}` : areaFilter !== '' && `Danh sách giảng viên quan tâm lĩnh vực ${areaFilterObj && areaFilterObj.name}` }
                            </div>
                            { isLoadingLecturers && <div class="text-center">
                                <i class="fa fa-circle-o-notch fa-spin fa-fw"></i>
                                <span class="sr-only">Loading...</span>
                            </div> }
                            { lecturers.length > 0 && !isLoadingLecturers && (departmentFilter !== '' || areaFilter !== '') && <div class="col-md-12 table-responsive">
                                <table class="table table-hover table-condensed">
                                    <thead>
                                        <tr>
                                            <th>HHHV</th>
                                            <th>Tên giảng viên</th>
                                            <th>Đơn vị</th>
                                            <th>VNU email</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        { lecturers.map(lecturer => {
                                            const degree = degrees.list.find(d => d.id == lecturer.degreeId)
                                            const department = departments.list.find(d => d.id == lecturer.departmentId)
                                            return (
                                                <tr key={lecturer.id} class="clickable" onClick={e => this.handleClickLecturer(lecturer.id)}>
                                                    <td>{degree && degree.name}</td>
                                                    <td>{lecturer.fullname}</td>
                                                    <td>{department && department.name}</td>
                                                    <td>{lecturer.vnuMail}</td>
                                                </tr>
                                        )})}
                                    </tbody>
                                </table>
                            </div> }
                            { lecturers.length == 0 && !isLoadingLecturers && (departmentFilter !== '' || areaFilter !== '') && <div>Hiện chưa có giảng viên.</div> }
                        </div>
                    </div>
                </div>
            </div>
        )
    }
}

export default BrowseLecturers
