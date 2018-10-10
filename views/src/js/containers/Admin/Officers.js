import { bindActionCreators } from 'redux'
import { connect } from 'react-redux'
import { AdminOfficers } from 'Components'
import { adminDepartmentActions, adminOfficerActions, sharedActions } from 'Actions'

const mapStateToProps = state => {
    return {
        officers: state.adminOfficers,
        departments: state.adminDepartments,
        degrees: state.degrees,
        auth: state.auth
    }
}

const mapDispatchToProps = dispatch => {
    const { loadDepartmentOfFaculty } = adminDepartmentActions
    const { loadDegrees } = sharedActions
    return { actions: bindActionCreators(Object.assign({}, adminOfficerActions, {loadDepartmentOfFaculty}, {loadDegrees}), dispatch) }
}

export default connect(mapStateToProps, mapDispatchToProps)(AdminOfficers)
