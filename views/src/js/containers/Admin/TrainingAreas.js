import React, { Component } from 'react'
import { bindActionCreators } from 'redux'
import { connect } from 'react-redux'
import { AdminTrainingAreas } from 'Components'
import { adminTrainingActions, sharedActions } from 'Actions'

const mapStateToProps = state => {
    return { areas: state.adminTrainingAreas }
}

const mapDispatchToProps = dispatch => {
    const { loadTrainingAreas } = sharedActions
    return { actions: bindActionCreators(Object.assign(adminTrainingActions, {loadTrainingAreas}), dispatch) }
}

export default connect(mapStateToProps, mapDispatchToProps)(AdminTrainingAreas)
