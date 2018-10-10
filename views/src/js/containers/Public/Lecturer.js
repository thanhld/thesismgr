import React, { Component } from 'react'
import { connect } from 'react-redux'
import { bindActionCreators } from 'redux'
import { PublicLecturer } from 'Components'
import { sharedActions } from 'Actions'

const mapStateToProps = state => {
    return {
        lecturer: state.publicLecturer.data,
        areas: state.publicLecturer.areas,
        degrees: state.degrees,
        departments: state.departments
    }
}

const mapDispatchToProps = dispatch => {
    const { loadPublicLecturer, loadDegrees, loadDepartments, loadPublicLecturerAreas } = sharedActions
    return { actions: bindActionCreators({loadPublicLecturer, loadDegrees, loadDepartments, loadPublicLecturerAreas}, dispatch) }
}

export default connect(mapStateToProps, mapDispatchToProps)(PublicLecturer)
