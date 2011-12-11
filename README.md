![Aptgraph](http://aptgraph.com/images/logo_on_grey.png)

Usage is pretty simple.

	// Get a instance of the API (while passing in your apikey)
	$aptgraph = new Aptgraph('Bc9dsdjhJHhdsf&54s34cv49Kfasducv7');

	// Add a couple of random values to this graph, then send it to Aptgraph
	// Note that 8jspy is this graphs id.
	$aptgraph
		->add('8jspy',rand(1,1000))
		->add('8jspy',rand(1,1000))
		->add('8jspy',rand(1,1000))
		->send();

See Aptgraph for more details
