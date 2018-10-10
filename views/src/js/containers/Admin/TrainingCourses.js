import React, { Component } from 'react'
import { bindActionCreators } from 'redux'
import { connect } from 'react-redux'
import { AdminTrainingCourses } from 'Components'
import { adminTrainingActions, sharedActions } from 'Actions'

const mapStateToProps = state => {
    return {
        courses: state.adminTrainingCourses,
        programs: state.adminTrainingPrograms
    }
}

const mapDispatchToProps = dispatch => {
    const { loadTrainingPrograms } = sharedActions
    return { actions: bindActionCreators({...adminTrainingActions, loadTrainingPrograms}, dispatch) }
}

export default connect(mapStateToProps, mapDispatchToProps)(AdminTrainingCourses)
