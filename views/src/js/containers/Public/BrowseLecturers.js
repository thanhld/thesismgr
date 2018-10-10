import React, { Component } from 'react'
import { connect } from 'react-redux'
import { bindActionCreators } from 'redux'
import { BrowseLecturers } from 'Components'
import { sharedActions } from 'Actions'

const mapStateToProps = state => {
    return {
        userFaculty: state.auth.user.facultyId,
        departments: state.departments,
        areas: state.knowledgeAreas,
        degrees: state.degrees,
        lecturers: state.browseLecturers.lecturers
    }
}

const mapDispatchToProps = dispatch => {
    return { actions: bindActionCreators(sharedActions, dispatch) }
}

export default connect(mapStateToProps, mapDispatchToProps)(BrowseLecturers)
