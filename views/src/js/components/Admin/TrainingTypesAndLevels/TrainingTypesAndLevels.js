import React, { Component } from 'react'

class AdminTrainingTypesAndLevels extends Component {
    componentWillMount() {
        const { types, levels, actions } = this.props
        if (!types.isLoaded) actions.loadTrainingTypes()
        if (!levels.isLoaded) actions.loadTrainingLevels()
    }
    render() {
        const { types, levels } = this.props
        return (
            <div>
                <div>
                    <div class="row">
                        <div class="col-xs-9">
                            <div class="page-title">Hệ đào tạo</div>
                        </div>
                    </div>
                    <br />
                    <div class="col-md-6 col-md-offset-3 table-responsive">
                        <table class="table table-hover table-condensed">
                            <thead>
                                <tr>
                                    <th>TT</th>
                                    <th>Tên hệ đào tạo</th>
                                </tr>
                            </thead>
                            <tbody>
                                { types.list && types.list.map((type, index) => { return (
                                    <tr key={type.id}>
                                        <td>{index+1}</td>
                                        <td>{type.name}</td>
                                    </tr>
                                )}) }
                            </tbody>
                        </table>
                    </div>
                </div>
                <br />
                <br />
                <br />
                <div>
                    <div class="row">
                        <div class="col-xs-9">
                            <div class="page-title">Bậc đào tạo</div>
                        </div>
                    </div>
                    <br />
                    <div class="col-md-6 col-md-offset-3 table-responsive">
                        <table class="table table-hover table-condensed">
                            <thead>
                                <tr>
                                    <th>TT</th>
                                    <th>Tên bậc đào tạo</th>
                                </tr>
                            </thead>
                            <tbody>
                                { levels.list && levels.list.map((level, index) => { return (
                                    <tr key={level.id}>
                                        <td>{index+1}</td>
                                        <td>{level.name}</td>
                                    </tr>
                                )}) }
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        )
    }
}

export default AdminTrainingTypesAndLevels
