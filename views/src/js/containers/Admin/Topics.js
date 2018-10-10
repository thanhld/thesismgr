import React, { Component } from 'react'
import { connect } from 'react-redux'
import { bindActionCreators } from 'redux'
import { AdminTopics } from 'Components'
import { sharedActions, adminTopicActions, adminDocumentActions, adminDepartmentActions } from 'Actions'

const mapStateToProps = state => {
    return {
        degrees: state.degrees,
        lecturers: state.lecturers,
        departments: state.adminDepartments,
        topics: state.adminTopics,
        facultyId: state.auth.user.facultyId
    }
}

const mapDispatchToProps = dispatch => {
    const { createDocument } = adminDocumentActions
    const { loadDepartmentOfFaculty } = adminDepartmentActions
    const { loadDegrees, loadLecturers, loadTopics, createActivity, uploadFile, flushTopics } = sharedActions
    return { actions: bindActionCreators({...adminTopicActions, loadDepartmentOfFaculty, loadDegrees, loadLecturers, loadTopics, createActivity, uploadFile, createDocument}, dispatch) }
}

export default connect(mapStateToProps, mapDispatchToProps)(AdminTopics)
