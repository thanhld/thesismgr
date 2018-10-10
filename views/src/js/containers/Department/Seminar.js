import { bindActionCreators } from 'redux'
import { connect } from 'react-redux'
import { DepartmentSeminar } from 'Components'
import { sharedActions, adminDepartmentActions } from 'Actions'

const mapStateToProps = state => {
    return {
        topics: state.departmentSeminar,
        degrees: state.degrees,
        lecturers: state.lecturers,
        departments: state.adminDepartments,
        uid: state.auth.user.uid,
        facultyId: state.auth.user.facultyId
    }
}

const mapDispatchToProps = dispatch => {
    const { loadTopics, loadDegrees, loadLecturers } = sharedActions
    const { loadDepartmentOfFaculty } = adminDepartmentActions
    return { actions: bindActionCreators({loadTopics, loadDegrees, loadLecturers, loadDepartmentOfFaculty}, dispatch) }
}

export default connect(mapStateToProps, mapDispatchToProps)(DepartmentSeminar)
