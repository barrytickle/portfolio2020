<?php include $_SERVER['DOCUMENT_ROOT'].'/includes/header.php' ?>
<header class="blog-header columns two-col">
  <section class="section--details flex flex-column flex-justify-center flex-align-center">
    <div class="content">
      <a href="#">Go Back</a>
      <h1>Maintaining 200 clients</h1>
      <p>
        Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.
      </p>
      <span>April 5, 2018</span>
    </div>
  </section>
  <section class="section-image" style="background-image:url('http://barrytickle.com/blog/wp-content/uploads/2017/08/jeremy-bishop-289214-min.jpg');">

  </section>
</header>


<main class="blog-main">
  <div class="smallContainer">

    <p>Judging from the title, you’re probably thinking that maintaining that many clients isn’t the easiest job in the world. You’d be right in thinking that, the question is how do you keep on top of that many websites in a small group of 4 web developers? Throughout this article, this is my solution while working for Physio123 to compose a solution for a large scale client base.</p>
<h2>What’s the problem?</h2>
<p>The problem currently is that 200 forms need maintaining across a large scale of customer sites. Adding spam protection to contact forms across already existing sites can take a long time to produce a solution for. So what’s the solution? My proposed solution is to build a database management system, which can connect with all 200+ sites.</p>
<p><img class="alignnone size-full wp-image-75" src="http://barrytickle.com/blog/wp-content/uploads/2018/04/client-form-capture.jpg" alt="" width="1276" height="665" srcset="http://barrytickle.com/blog/wp-content/uploads/2018/04/client-form-capture.jpg 1276w, http://barrytickle.com/blog/wp-content/uploads/2018/04/client-form-capture-300x156.jpg 300w, http://barrytickle.com/blog/wp-content/uploads/2018/04/client-form-capture-768x400.jpg 768w, http://barrytickle.com/blog/wp-content/uploads/2018/04/client-form-capture-1024x534.jpg 1024w" sizes="(max-width: 1276px) 100vw, 1276px"></p>
<p>Meet the form generator, this generator currently uses Javascript + Bootstrap 4 to propose an interface for potential developers who need to maintain Physio123 sites. What this system will be currently doing is generating a form using JSON.</p>
<p>To kickstart the process, first the developer must fill out the form on the left, this form allows the option to modify the label, the placeholder the input (text, textarea, password, email, radio, checkbox, dropdown), the class, is the field itself required (such as an email address) and a name attribute.</p>
<p><img class="alignnone size-full wp-image-78" src="http://barrytickle.com/blog/wp-content/uploads/2018/04/29791240_1029092587266415_1406380547084648448_n.png" alt="" width="706" height="43" srcset="http://barrytickle.com/blog/wp-content/uploads/2018/04/29791240_1029092587266415_1406380547084648448_n.png 706w, http://barrytickle.com/blog/wp-content/uploads/2018/04/29791240_1029092587266415_1406380547084648448_n-300x18.png 300w" sizes="(max-width: 706px) 100vw, 706px"></p>
<p>The following json&nbsp;code will be generated per field, this will contain all the attributes mentioned above, however the option value will remain empty. Due to this being a text field only, the option field will store an array value for radio buttons, check boxes and dropdown menu’s. This is to allow multiple labels to be generated throughout within the final form.</p>
<p>&nbsp;</p>
<h2>How to control 200+ forms sending.</h2>
<p>Generating a large scale of forms is the easy part of the task, the difficult part is discovering how these forms will actually send. Or is it actually difficult at all? Once a form is submitted to a database, there is control over where the form will send to which is convenient&nbsp;for forms which need to send to different emails on the same site.</p>
<p>However, the magic behind sending a form can be found below. What happens here is the email template actually generates itself, dependant on what is being sent to the PHP processor file.</p>
<p><img class="alignnone  wp-image-77" src="http://barrytickle.com/blog/wp-content/uploads/2018/04/blog-post-code.png" alt="" width="1110" height="558" srcset="http://barrytickle.com/blog/wp-content/uploads/2018/04/blog-post-code.png 1110w, http://barrytickle.com/blog/wp-content/uploads/2018/04/blog-post-code-300x151.png 300w, http://barrytickle.com/blog/wp-content/uploads/2018/04/blog-post-code-768x386.png 768w, http://barrytickle.com/blog/wp-content/uploads/2018/04/blog-post-code-1024x515.png 1024w" sizes="(max-width: 1110px) 100vw, 1110px"></p>
<p>Within this example, row-template.html is a segment of the table this will generate a row for each field that is sent to the file. For example one form may contain (Name, email, comment), while another form may contain (Name, Email, Phone Number, Location, Comment). Normally, these are sent to two different processor files to statically work for these values, however using this looping method cuts down the amount of work used.</p>
<p>What is meant by this is that the $_POST[] method in which PHP uses to store form values can actually be looped through, which is being done within the following code.&nbsp;Doing this can dynamically generate a unique html&nbsp;email for each form to be sent to its corresponding address.</p>
<p>All this data will be gathered from the database, dependant on the site it is on.</p>
<p>*Note this is still a concept, production has not yet been started.</p>
 <!-- The blog post text extract -->


  </div>
</main>
<?php include $_SERVER['DOCUMENT_ROOT'].'/includes/footer.php' ?>
