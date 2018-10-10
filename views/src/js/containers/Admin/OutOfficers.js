import { bindActionCreators } from 'redux'
import { connect } from 'react-redux'
import { AdminOutOfficers } from 'Components'
import { adminDepartmentActions, adminTopicActions, sharedActions } from 'Actions'

const mapStateToProps = state => {
    return {
        departments: state.adminDepartments,
        degrees: state.degrees,
        topics: state.adminOutTopics,
        outOfficers: state.outOfficers,
        lecturers: state.lecturers,
        facultyId: state.auth.user.facultyId
    }
}

const mapDispatchToProps = dispatch => {
    const { loadDepartmentOfFaculty } = adminDepartmentActions
    const { flushTopics, createOutOfficers } = adminTopicActions
    const { loadDegrees, loadTopics, loadOutOfficers, loadLecturers, createActivity } = sharedActions
    return { actions: bindActionCreators({loadDepartmentOfFaculty, loadDegrees, loadTopics, loadOutOfficers, loadLecturers, flushTopics, createOutOfficers, createActivity}, dispatch) }
}

export default connect(mapStateToProps, mapDispatchToProps)(AdminOutOfficers)
