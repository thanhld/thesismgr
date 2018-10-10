import React, { Component } from 'react'
import { bindActionCreators } from 'redux'
import { connect } from 'react-redux'
import { AdminLearners } from 'Components'
import { adminLearnerActions, adminTrainingActions } from 'Actions'

const mapStateToProps = state => {
    return {
        learners: state.adminLearners,
        courses: state.adminTrainingCourses
    }
}

const mapDispatchToProps = dispatch => {
    const { loadTrainingCourses } = adminTrainingActions
    return { actions: bindActionCreators(Object.assign({}, adminLearnerActions, {loadTrainingCourses}), dispatch) }
}

export default connect(mapStateToProps, mapDispatchToProps)(AdminLearners)
