import { bindActionCreators } from 'redux'
import { connect } from 'react-redux'
import { SuperuserDegrees } from 'Components'
import { superuserActions, sharedActions } from 'Actions'

const mapStateToProps = state => {
    return { degrees: state.degrees }
}

const mapDispatchToProps = dispatch => {
    const { loadDegrees } = sharedActions
    return { actions: bindActionCreators(Object.assign(superuserActions, {loadDegrees}), dispatch) }
}

export default connect(mapStateToProps, mapDispatchToProps)(SuperuserDegrees)
