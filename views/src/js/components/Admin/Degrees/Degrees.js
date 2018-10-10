import React, { Component } from 'react'

class AdminDegrees extends Component {
    componentWillMount() {
        const { degrees, actions } = this.props
        const { isLoaded } = degrees
        if (!isLoaded) actions.loadDegrees()
    }
    render() {
        const { degrees } = this.props
        return (
            <div>
                <div class="row">
                    <div class="col-xs-9">
                        <div class="page-title">Học hàm, học vị</div>
                    </div>
                </div>
                <br />
                <div class="col-md-8 col-md-offset-2 table-responsive">
                    <table class="table table-hover table-condensed">
                        <thead>
                            <tr>
                                <th>TT</th>
                                <th>Tên học hàm, học vị</th>
                            </tr>
                        </thead>
                        <tbody>
                            { degrees.list && degrees.list.map((degree, index) => { return (
                                <tr key={degree.id}>
                                    <td>{index+1}</td>
                                    <td>{degree.name}</td>
                                </tr>
                            )})}
                        </tbody>
                    </table>
                </div>
            </div>
        )
    }
}

export default AdminDegrees
