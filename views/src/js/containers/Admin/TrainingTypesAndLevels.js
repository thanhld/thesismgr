import { bindActionCreators } from 'redux'
import { connect } from 'react-redux'
import { AdminTrainingTypesAndLevels } from 'Components'
import { sharedActions } from 'Actions'

const mapStateToProps = state => {
    return {
        types: state.trainingTypes,
        levels: state.trainingLevels
    }
}

const mapDispatchToProps = dispatch => {
    let { loadTrainingTypes, loadTrainingLevels } = sharedActions
    return { actions: bindActionCreators(Object.assign({loadTrainingTypes}, {loadTrainingLevels}), dispatch) }
}

export default connect(mapStateToProps, mapDispatchToProps)(AdminTrainingTypesAndLevels)
