var mysql      = require('mysql');
var connection = mysql.createConnection({
  host     : 'localhost',
  user     : 'thesismgr_user',
  password : 'QLRaSJVv1QJA5xxy',
  database : 'thesismgr'
});

connection.connect();

connection.query('UPDATE topics t INNER JOIN topics_histories th ON t.id = th.topicId
        SET t.topicStatus = (
            CASE WHEN DATE(th.startPauseDate) <= DATE(NOW()) AND DATE(DATE_ADD(th.startPauseDate, INTERVAL th.pauseDuration MONTH)) > DATE(NOW()) THEN 303
                WHEN DATE(th.startPauseDate) > DATE(NOW()) THEN 887
                ELSE 888
            END
        )
        WHERE t.topicStatus = 887 OR t.topicStatus = 303;', function (error, results, fields) {
  if (error) throw error;
});

connection.end();
