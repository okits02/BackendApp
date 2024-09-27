<?php
    class DBOperation{
        private $con;
        function __construct()
        {   
            require_once dirname(__FILE__) .'\DBconnect.php';
            $db = new Dbconnect();
            $this->con=$db->connect();
        }
        public function createUser($userName, $userPassword, $email, $phone)
        {
            if($this->isuserExit($userName, $email))
            {
                return 0;
            }else
            {
            $userPassword=md5($userPassword);
            $stmt=$this->con->prepare("INSERT INTO `user` (`ID`, `UserName`, `UserPassword`, `Email`, `userPhone`)
             VALUES (NULL, ?, ?, ?, ?);");
            $stmt->bind_param("ssss",$userName,$userPassword,$email, $phone);
            
            if($stmt->execute())
            {
                return 1;
            }  else {
                return 2;
            }
            }
        }
        public function userLogin($UserName, $UserPassword){
            $password=md5($UserPassword);
            $stmt=$this->con->prepare("SELECT ID FROM user WHERE UserName = ? AND UserPassword = ?");
            $stmt->bind_param("ss",$UserName, $password);
            $stmt->execute();
            $stmt->store_result();
            return $stmt->num_rows()>0;
        }
        public function getUserByUserName($UserName)
        {
            $stmt=$this->con->prepare("SELECT * FROM `user` WHERE UserName = ?");
            $stmt->bind_param("s",$UserName);
            $stmt->execute();
            return $stmt->get_result()->fetch_assoc();
        }
        private function isuserExit($userName, $email){
            $stmt=$this->con->prepare("SELECT ID FROM user WHERE UserName=? OR Email=?");
            $stmt->bind_param("ss",$userName, $email);
            $stmt->execute();
            $stmt->store_result();
            return $stmt->num_rows()>0;
        }
        public function changePassword($UserName, $oldpassword, $newpassword)
        {
            $stmt = $this->con->prepare("SELECT ID FROM user WHERE UserName = ? AND UserPassword = ?");
            $stmt->bind_param("ss", $UserName, md5($oldpassword));
            $stmt->execute();
            $stmt->store_result();
            if ($stmt->num_rows() > 0) {
                $newpassword = md5($newpassword);
                $stmt = $this->con->prepare("UPDATE user SET UserPassword = ? WHERE UserName = ?");
                $stmt->bind_param("ss", $newpassword, $UserName);
                if ($stmt->execute()) {
                    return 1;
                } else {
                    return 0;
                }
            } else {
                return -1;
            }
        }
        
        public function getMoviesUp_Comming()
        {
            $stmt=$this->con->prepare("SELECT * FROM up_coming");
            $stmt->execute();
            return $stmt->get_result();
        }
        public function getMoviespopular()
        {
            $stmt=$this->con->prepare("SELECT * FROM now_playing");
            $stmt->execute();
            return $stmt->get_result();
        }
        public function getGenre()
        {
            $stmt=$this->con->prepare("SELECT * FROM genre");
            $stmt->execute();
            return $stmt->get_result();
        }
        public function getAllMovies()
        {
            $stmt=$this->con->prepare('SELECT * FROM movies');
            $stmt->execute();
            return $stmt->get_result();
        }
        public function getMovies($movies_id)
        {
            $stmt=$this->con->prepare("SELECT * FROM movies WHERE id=?");
            $stmt->bind_param("i",$movies_id);
            $stmt->execute();
            return $stmt->get_result()->fetch_assoc();
        }
        public function getGenreByID($genre_ids)
        {
            $stmt=$this->con->prepare("SELECT name FROM genre WHERE id=?");
            $stmt->bind_param("s", $genre_ids);
            $stmt->execute();
            return $stmt->get_result()->fetch_assoc();
        }
        public function getMovies_search()
        {
            $stmt=$this->con->prepare("SELECT * FROM movies");
            $stmt->execute();
            return $stmt->get_result();
        }
        public function addToFavorite($userName,$movies_id)
        {
            $stmt=$this->con->prepare("SELECT * FROM favorite WHERE UserName = ? AND movie_ids = ?");
            $stmt->bind_param("si", $userName, $movies_id);
            $stmt->execute();
            $result=$stmt->get_result();
            if($result->num_rows==0)
            {
                $stmt=$this->con->prepare("INSERT INTO favorite (UserName, movie_ids) VALUES (?, ?)");
                $stmt->bind_param("si",$userName,$movies_id);
                if($stmt->execute())
                {
                    return true;
                }else{
                    return false;
                }
            }else{
                return false;
            }
        }
        public function getMoviesIDbyUserName($username)
        {
            $stmt=$this->con->prepare("SELECT movie_ids FROM favorite WHERE UserName = ?");
            $stmt->bind_param("s", $username);
            $stmt->execute();
            return $stmt->get_result();
        }
        public function getMoviesByMoviesID($moviesID){
            $stmt=$this->con->prepare("SELECT*FROM movies WHERE id=?");
            $stmt->bind_param("i", $moviesID);
            $stmt->execute();
            return $stmt->get_result();
        }
        public function getMoviesByGenre($genreid)
        {
            $stmt=$this->con->prepare("SELECT*FROM movies WHERE genre_ids LIKE CONCAT('%', ?, '%')");
            $stmt->bind_param("i", $genreid);
            $stmt->execute();
            return $stmt->get_result();
        }
        public function DropFavoriteItem($username, $movie_ids)
        {
            $stmt=$this->con->prepare("DELETE FROM favorite WHERE UserName=? AND movie_ids=?");
            $stmt->bind_param("si", $username, $movie_ids);
            if($stmt->execute())
            {
                return true;
            }else
            {
                return false;
            }
        }
        public function AddMovies($backdrop_path, $genre_ids, $original_language, $original_title, $overview, $popularity, $poster_path, $release_date, $runtime, $production_companies, $title, $vote_average, $vote_count, $vieo_link)
        {
            $stmt=$this->con->prepare('INSERT INTO `movies` (`id`, `backdrop_path`, `genre_ids`, `original_language`, `original_title`, `overview`, `popularity`, `poster_path`, `release_date`, `runtime`, `production_companies`, `title`, `vote_average`, `vote_count`, `video_link`) VALUES (NULL, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)');
            $stmt->bind_param("ssssssssisssis", $backdrop_path, $genre_ids, $original_language, $original_title, $overview, $popularity, $poster_path, $release_date, $runtime, $production_companies, $title, $vote_average, $vote_count, $vieo_link);
            if($stmt->execute())
            {
                return true;
            }else{
                return false;
            }
        }

        public function AddUp_coming($backdrop_path, $genre_ids, $original_language, $original_title, $overview, $popularity, $poster_path, $release_date, $title, $vote_average, $vote_count, $vieo_link){
            $stmt=$this->con->prepare('INSERT INTO `up_coming` (`id`, `backdrop_path`, `genre_ids`, `original_language`, `original_title`, `overview`, `popularity`, `poster_path`, `release_date`, `title`, `vote_average`, `vote_count`, `video_link`) VALUES (NULL, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)');
            $stmt->bind_param("ssssssssssis", $backdrop_path, $genre_ids, $original_language, $original_title, $overview, $popularity, $poster_path, $release_date, $title, $vote_average, $vote_count, $vieo_link);
            if($stmt->execute())
            {
                return true;
            }else {
                return false;
            }
        }

        public function AddPopular($backdrop_path, $genre_ids, $original_language, $original_title, $overview, $popularity, $poster_path, $release_date, $title, $vote_average, $vote_count, $vieo_link){
            $stmt=$this->con->prepare('INSERT INTO `up_coming` (`id`, `backdrop_path`, `genre_ids`, `original_language`, `original_title`, `overview`, `popularity`, `poster_path`, `release_date`, `title`, `vote_average`, `vote_count`, `video_link`) VALUES (NULL, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)');
            $stmt->bind_param("ssssssssssis", $backdrop_path, $genre_ids, $original_language, $original_title, $overview, $popularity, $poster_path, $release_date, $title, $vote_average, $vote_count, $vieo_link);
            if($stmt->execute())
            {
                return true;
            }else {
                return false;
            }
        }

        public function AddGenre($id, $name)
        {
            $stmt=$this->con->prepare('INSERT INTO `genre` (`id`, `name`) VALUES (NULL, ?)');
            $stmt->bind_param("s", $name);
            if($stmt->execute())
            {
                return true;
            }else
            {
                return false;
            }
        }

        public function DropUser($UserName)
        {
            $stmt=$this->con->prepare('SELECT * FROM user WHERE UserName=?');
            $stmt->bind_param("s", $UserName);
            $stmt->execute();
            $result=$stmt->get_result();
            if($result->num_rows > 0)
            {
                $stmt=$this->con->prepare('DELETE FROM user WHERE UserName=?');
                $stmt->bind_param("s", $UserName);
                if($stmt->execute())
                {
                    return 1;
                }else{
                    return 0;
                }
            }else
            {
                return -1;
            }
        }
        
        public function DropMovies($MoviesName)
        {
            $stmt=$this->con->prepare('SELECT * FROM movies WHERE title=?');
            $stmt->bind_param("s", $MoviesName);
            $stmt->execute();
            $result=$stmt->get_result();
            if($result->num_rows>0)
            {
                $stmt=$this->con->prepare('DELETE FROM movies WHERE title=?');
                if($stmt->execute())
                {
                    return 1;
                }else {
                    return 0;
                }
            }else {
                return -1;
            }
        }

        public function DropMoviesOnUp_coming($MoviesName)
        {
            $stmt=$this->con->prepare('SELECT * FROM up_coming WHERE title=?');
            $stmt->bind_param("s", $MoviesName);
            $stmt->execute();
            $result=$stmt->get_result();
            if($result->num_rows>0)
            {
                $stmt=$this->con->prepare('DELETE FROM up_coming WHERE title=?');
                if($stmt->execute())
                {
                    return 1;
                }else {
                    return 0;
                }
            }else {
                return -1;
            }
        }
        
        public function DropMoviesOnPopular($MoviesName)
        {
            $stmt=$this->con->prepare('SELECT * FROM popular WHERE title=?');
            $stmt->bind_param("s", $MoviesName);
            $stmt->execute();
            $result=$stmt->get_result();
            if($result->num_rows>0)
            {
                $stmt=$this->con->prepare('DELETE FROM popular WHERE title=?');
                if($stmt->execute())
                {
                    return 1;
                }else {
                    return 0;
                }
            }else {
                return -1;
            }
        }
    }