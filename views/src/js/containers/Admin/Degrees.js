import { bindActionCreators } from 'redux'
import { connect } from 'react-redux'
import { AdminDegrees } from 'Components'
import { sharedActions } from 'Actions'

const mapStateToProps = state => {
    return { degrees: state.degrees }
}

const mapDispatchToProps = dispatch => {
    let { loadDegrees } = sharedActions
    return { actions: bindActionCreators({loadDegrees}, dispatch) }
}

export default connect(mapStateToProps, mapDispatchToProps)(AdminDegrees)
