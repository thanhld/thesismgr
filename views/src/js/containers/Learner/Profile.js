import React, { Component } from 'react'
import { bindActionCreators } from 'redux'
import { connect } from 'react-redux'
import { LearnerProfile } from 'Components'
import { learnerActions } from 'Actions'

const mapStateToProps = state => {
    return {
        user: state.auth.user,
        learner: state.learnerProfile
    }
}

const mapDispatchToProps = dispatch => {
    return {
        actions: bindActionCreators(learnerActions, dispatch)
    }
}

export default connect(mapStateToProps, mapDispatchToProps)(LearnerProfile)
