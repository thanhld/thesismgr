import { bindActionCreators } from 'redux'
import { connect } from 'react-redux'
import { Login } from 'Components'
import { authActions } from 'Actions'

const mapStateToProps = state => {
    // return { auth: state.auth }
}

const mapDispatchToProps = dispatch => {
    return { actions: bindActionCreators(authActions, dispatch) }
}

export default connect(null, mapDispatchToProps)(Login)
