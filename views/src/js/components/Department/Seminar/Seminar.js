import React, { Component } from 'react'
import TopicsList from '../../Admin/Topics/TopicsList'
import { DEPARTMENT_LOAD_SEMINAR } from 'Constants'
import { Loading } from 'Components'

class DepartmentSeminar extends Component {
    constructor(props) {
        super(props)
        this.state = {
            activePage: 1,
            lowerStatus: 897, // const
            higherStatus: 900, // const
            type: 2
        }
    }
    reloadData = callback => {
        const { actions } = this.props
        const { lowerStatus, higherStatus, type } = this.state
        const filter = `topicStatus>=${lowerStatus},topicStatus<=${higherStatus},topicType=${type}`
        actions.loadTopics(DEPARTMENT_LOAD_SEMINAR, filter).then(() => {
            if (callback) callback()
        })
    }
    componentWillMount() {
        const { facultyId, actions, departments, degrees, lecturers } = this.props
        if (!degrees.isLoaded) actions.loadDegrees()
        if (!lecturers.isLoaded) actions.loadLecturers()
        if (!departments.isLoaded) actions.loadDepartmentOfFaculty(facultyId)
        this.reloadData()
    }
    handlePageChange = pageNum => {
        this.setState({
            activePage: pageNum
        })
    }
    render() {
        const { topics: { list }, departments, degrees, lecturers } = this.props
        const { activePage, lowerStatus, higherStatus } = this.state

        if (!lecturers.isLoaded) return <Loading />
        if (!degrees.isLoaded) return <Loading />
        if (!departments.isLoaded) return <Loading />
        if (!this.props.topics.isLoaded) return <Loading />

        return <div>
            <div class="row">
                <div class="col-xs-9 page-title">Kiểm tra tiến độ luận văn cao học</div>
            </div>
            <TopicsList
                topics={list}
                activePage={activePage}
                degrees={degrees.list}
                lecturers={lecturers.list}
                departments={departments.list}
                lowerStatus={lowerStatus}
                higherStatus={higherStatus}
                reloadData={this.reloadData}
                handlePageChange={this.handlePageChange}
            />
        </div>
    }
}

export default DepartmentSeminar
