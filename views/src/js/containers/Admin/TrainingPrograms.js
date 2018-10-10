import React, { Component } from 'react'
import { bindActionCreators } from 'redux'
import { connect } from 'react-redux'
import { AdminTrainingPrograms } from 'Components'
import { adminDepartmentActions, adminTrainingActions, sharedActions } from 'Actions'

const mapStateToProps = state => {
    return {
        programs: state.adminTrainingPrograms,
        areas: state.adminTrainingAreas,
        types: state.trainingTypes,
        levels: state.trainingLevels,
        departments: state.adminDepartments,
        facultyId: state.auth.user.facultyId
    }
}

const mapDispatchToProps = dispatch => {
    const { loadTrainingAreas, loadTrainingPrograms, loadTrainingTypes, loadTrainingLevels } = sharedActions
    const { loadDepartmentOfFaculty } = adminDepartmentActions
    return { actions: bindActionCreators({...adminTrainingActions, loadTrainingAreas, loadTrainingPrograms, loadTrainingTypes, loadTrainingLevels, loadDepartmentOfFaculty}, dispatch) }
}

export default connect(mapStateToProps, mapDispatchToProps)(AdminTrainingPrograms)
