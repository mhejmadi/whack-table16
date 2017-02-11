module Counter exposing (main)

import Html exposing (Html, div, text, button, input, p, h1, h3)
import Html.Attributes exposing (..)
import Html.Events exposing (onClick, onInput)


type alias Model =
  { review : String
  , title : String
  , username : String
  , reviews : List Review
  }


model : Model
model =
  { review = ""
  , title = ""
  , username = ""
  , reviews = []
  }

type alias Review =
  { title : String
  , username : String
  , content : String
  }

renderReview : Review -> Html Msg
renderReview review =
  div [] [ h1 [] [ text review.title ]
    , h3 [] [ text "by: ", text review.username ]
    , p [] [ text review.content ]
    ]


type Msg
  = UpdateReview String
    | UpdateTitle String
    | UpdateUsername String
    | SaveReview


update : Msg -> Model -> ( Model, Cmd Msg )
update msg model =
  case msg of
    UpdateReview newReview ->
      ( { model | review = newReview }
      , Cmd.none )

    UpdateUsername newName ->
      ( { model | username = newName }
      , Cmd.none )

    UpdateTitle newTitle ->
      ( { model | title = newTitle }
      , Cmd.none )

    SaveReview ->
      ( { model | title = "", username = "", review = "", reviews = { title = model.title, username = model.username, content = model.review } :: model.reviews }
      , Cmd.none )


view : Model -> Html Msg
view model =
  div []
    [ div [] (List.map renderReview model.reviews )
    , input [ class "example", value model.title, onInput UpdateTitle, placeholder "Title" ] [ ]
    , input [ value model.username, onInput UpdateUsername, placeholder "User Name" ] [ ]
    , input [ value model.review, onInput UpdateReview, placeholder "Review" ] [ ]
    , button [ onClick SaveReview ] [ text "Save" ]
    ]


main : Program Never Model Msg
main =
  Html.program
    { view = view
    , update = update
    , subscriptions = \_ -> Sub.none
    , init = ( model, Cmd.none )
    }
