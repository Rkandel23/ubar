# Introduction #

Hybrid actions are [View Actions](ViewActions.md) with limited aspects of [Submit Actions](SubmitActions.md). An example would be a blog that takes a blog id for display. As these actions are a union of both types, you should see the wiki pages for both for further details. However, an example of the appropriate usage of a hybrid action appears below.

Action
```
<?php
class Blog extends BaseAction {

    private $blogId;

    private $post;

    private $comments = array ();

    private $posts = array ();

    public function getPost() {
        return $this->post;
    }

    public function getComments() {
        return $this->comments;
    }

    public function getPosts() {
        return $this->posts;
    }

    public function setBlogId($id) {
        $this->blogId = $id;
    }

    public function getBlogId() {
        return $this->blogId;
    }

    public function executeInner() {
        // asemble blog articles from database
        $dbManager = new DBManager();

        $result = null;

        // if specific id is set, get that blog and associated comments
        if (!is_null($this->blogId)) {
            $result = mysql_query("SELECT * FROM blog WHERE blogid = " . $this->blogId);
            if (!$result || mysql_num_rows($result) == 0) {
                $this->addError("blog.error.badId", array("id" =>$this->blogId));
            }
        }
        // if no id or bad id, get most recent post
        if (is_null($result) || !$result || mysql_num_rows($result) == 0) {
            $result = mysql_query("SELECT * FROM blog WHERE blogid = (SELECT MAX(blogid) FROM blog)");
            if (!$result || mysql_num_rows($result) == 0) {
                $this->addError("blog.error.noPosts");
            }
        }

        if ($result && mysql_num_rows($result) == 1) {
            // get post info
            $this->post = mysql_fetch_object($result, 'BlogPost');

            // add id to local vars
            $this->blogId = $this->post->getId();

            // set title
            $this->setTitle($this->post->getTitle());

            // free results
            mysql_free_result($result);

            // get comments associated with post and make available as list
            $result = mysql_query("SELECT * FROM blogcomments WHERE blogid = " . $this->post->getId());
            if (mysql_num_rows($result) > 0) {
                while ($row = mysql_fetch_object($result, 'BlogComment')) {
                    array_push($this->comments, $row);
                }
            }

            // free results
            mysql_free_result($result);

            // get all blog entries for listing
            $result = mysql_query("SELECT blogid, title, created FROM blog ORDER BY blogid DESC");
            if (mysql_num_rows($result) > 0) {
                while ($row = mysql_fetch_object($result, 'BlogPost')) {
                    array_push($this->posts, $row);
                }
            }
        }

        // else get most recent
        return GlobalConstants :: SUCCESS;
    }
}
?>
```

ubar.xml entry
```
<action name="Blog" path="blog.Blog" view="pages.blog" template="default" section="blog" titleKey="blog.title" />
```
Given that an invalid id cannot be used to display a blog post. You may choose to leave out a USER\_INPUT result and just fall through to a global result for ERROR (as appears above). Alternatively, you can:
  * A) Add a result that redirects you to the Blog action **without** an identifier, allowing you to default to the most recent post in your action.
  * B) Add a warning instead of an error and default to the most recent post in the original request.