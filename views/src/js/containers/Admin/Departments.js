import React, { Component } from 'react'
import { bindActionCreators } from 'redux'
import { connect } from 'react-redux'
import { AdminDepartments } from 'Components'
import { adminDepartmentActions } from 'Actions'

const mapStateToProps = state => {
    return {
        facultyId: state.auth.user.facultyId,
        departments: state.adminDepartments
    }
}

const mapDispatchToProps = dispatch => {
    return { actions: bindActionCreators(Object.assign(adminDepartmentActions), dispatch) }
}

export default connect(mapStateToProps, mapDispatchToProps)(AdminDepartments)
