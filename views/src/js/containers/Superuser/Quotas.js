import { bindActionCreators } from 'redux'
import { connect } from 'react-redux'
import { SuperuserQuotas } from 'Components'
import { superuserActions, sharedActions } from 'Actions'

const mapStateToProps = state => {
    return {
        degrees: state.degrees,
        quotas: state.superuserQuotas
    }
}

const mapDispatchToProps = dispatch => {
    const { loadQuotas, createQuota, updateQuota, changeActiveQuota, deleteQuota } = superuserActions
    const { loadDegrees } = sharedActions
    return { actions: bindActionCreators({loadQuotas, createQuota, updateQuota, changeActiveQuota, deleteQuota, loadDegrees}, dispatch) }
}

export default connect(mapStateToProps, mapDispatchToProps)(SuperuserQuotas)
