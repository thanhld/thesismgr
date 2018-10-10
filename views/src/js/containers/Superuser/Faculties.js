import { bindActionCreators } from 'redux'
import { connect } from 'react-redux'
import { SuperuserFaculties } from 'Components'
import { superuserActions, adminDepartmentActions } from 'Actions'

const mapStateToProps = state => {
    return { faculties: state.superuserFaculties }
}

const mapDispatchToProps = dispatch => {
    const { createDepartment } = adminDepartmentActions
    return { actions: bindActionCreators({...superuserActions, createDepartment}, dispatch) }
}

export default connect(mapStateToProps, mapDispatchToProps)(SuperuserFaculties)
