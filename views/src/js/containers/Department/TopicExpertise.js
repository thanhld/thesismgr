import { bindActionCreators } from 'redux'
import { connect } from 'react-redux'
import { DepartmentTopicExpertise } from 'Components'
import { sharedActions, lecturerActions, departmentActions } from 'Actions'

const mapStateToProps = state => {
    return {
        topics: state.departmentTopicExpertise,
        profile: state.lecturerProfile,
        degrees: state.degrees,
        lecturers: state.lecturers,
        uid: state.auth.user.uid
    }
}

const mapDispatchToProps = dispatch => {
    const { loadTopics, loadDegrees, loadLecturers, createActivity } = sharedActions
    const { flushTopics } = departmentActions
    const { loadLecturerInformation } = lecturerActions
    return { actions: bindActionCreators({flushTopics, loadTopics, loadDegrees, loadLecturers, createActivity, loadLecturerInformation}, dispatch) }
}

export default connect(mapStateToProps, mapDispatchToProps)(DepartmentTopicExpertise)
