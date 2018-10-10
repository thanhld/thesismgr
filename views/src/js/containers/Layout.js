import { bindActionCreators } from 'redux'
import { connect } from 'react-redux'
import { Layout } from 'Components'
import { authActions, lecturerActions, sharedActions } from 'Actions'

const mapStateToProps = state => {
    return {
        auth: state.auth,
        topics: state.lecturerTopics,
        adminOutTopics: state.adminOutTopics
    }
}

const mapDispatchToProps = dispatch => {
    const { loadTopics } = lecturerActions
    const adminLoadTopics = sharedActions.loadTopics
    return {
        actions: bindActionCreators({...authActions, loadTopics, adminLoadTopics}, dispatch)
    }
}

export default connect(mapStateToProps, mapDispatchToProps)(Layout)
